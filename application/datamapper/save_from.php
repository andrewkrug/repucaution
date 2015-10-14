<?php

/**
 * Usage examples:
 *
-simple: (save one table)
$post = array(
'username' => 'John', //key is a column_name, value is a value
'email' => 'johndoe@gmail.com'
);
$user = new User();
if ($user->save_from($post)) {
echo 'User saved or created!';
}

-advanced: (save obj and related_object_ids)
$post = array(
'username' => 'John',
'email' => 'johtdoe@gmail.com',
'user_hobby' => array(2,4,5,7)  //key is class name, value is array if ids
);
$user = new User();
if ($user->save_from($post,array('user_hobby'))) {
echo 'User saved or created, and related table updated';
}

-advanced 2: (save obj and related_object_fields)
$post = array(
'username' => 'John',
'email' => 'johtdoe@gmail.com',
'user_phone' => array(
0 => array('number' => '1111111'), //key is an index, value is array of ('table_column' => 'value')
1 => array('number' => '2222222')
)                            
);
$user = new User();
if ($user->save_from($post,array('user_phone'),TRUE)) {
echo 'newly created ids: ';
foreach($user->save_from->created_ids['user_phone'] as $id) {
echo $id;
}
} else {
echo 'error:' . $user->save_from->error; //empty if no error
}
 */

class DMZ_Save_from {

    /**
     * Convert an associative array back into a DataMapper model.
     *
     * If $fields is provided, missing fields are assumed to be empty checkboxes.
     * Alse if $sub_array is TRUE, will save related object provided by sub array
     *
     * @param    DataMapper $object The DataMapper Object to save to.
     * @param    array $data A an associative array of fields to convert.
     * @param    array $fields Array of 'safe' fields.  If empty, only includes the database columns.
     * @param    bool $sub_array If TRUE - will save provided by subarray related obj
     * @param    bool $save If TRUE, then attempt to save the object automatically.
     * @return    array|bool A list of newly related objects, or the result of the save if $save is TRUE.
     * If $sub_array TRUE, return array('error','created_ids')
     */
    function save_from($object, $data, $fields = '', $sub_array = FALSE, $save = TRUE) {
        // keep track of newly related objects
        $new_related_objects = array();

        $error = ''; //error string
        $created_ids = array(); //stores array of newly created ids ('related_class_name' => array(ids))
        // Assume all database columns.
        // In this case, simply store $fields that are in the $data array.

        $object_fields = $object->fields;
        if (in_array('password',$object_fields)) {
            $object_fields[] = 'password_confirm';
        }
        foreach($data as $k => $v) {
            if(in_array($k, $object_fields))
            {
                $object->{$k} = $v;
            }
        }

        if (!empty($fields)){
            // If $fields is provided, assume all $fields should exist.
            foreach($fields as $f)
            {
                if(array_key_exists($f, $object->has_one))
                {
                    // Store $has_one relationships
                    $c = get_class($object->{$f});
                    $rel = new $c();
                    $id = isset($data[$f]) ? $data[$f] : 0;
                    $rel->get_by_id($id);
                    if($rel->exists())
                    {
                        // The new relationship exists, save it.
                        $new_related_objects[$f] = $rel;
                    }
                    else
                    {
                        // The new relationship does not exist, delete the old one.
                        $object->delete($object->{$f}->get());
                    }
                }
                else if(array_key_exists($f, $object->has_many))
                {

                    // Store $has_many relationships
                    $c = get_class($object->{$f});
                    $ids = isset($data[$f]) ? $data[$f] : FALSE;
                    $has_join_table = false;
                    if (! empty($object->has_many[$f]['join_table'])) {
                        $has_join_table = true; //so, do not delete the record, only the relation
                    }

                    if ($sub_array) {
                        $error_classes_to_check = array();
                        $created_ids[$f] = array();
                        if (empty($ids)) {
                            $old_related_obj = $object->{$f}->select('id')->get();
                            if ($has_join_table) {
                                $object->delete($old_related_obj); //delete relation to related object
                            } else {
                                $old_related_obj->delete_all(); //delete related object
                            }
                        } else {
                            $related_ids = array();
                            foreach ($ids as $related_row) {
                                $create = false;
                                if (isset($related_row['id']) && $related_row['id'] ) {
                                    $id = $related_row['id'];
                                    $related_ids[] = $id; //add to list of provided ids
                                    unset($related_row['id']); //useless any more   
                                } else {
                                    $id = null;
                                    $create = true;
                                }

                                $related_obj = new $c($id);
                                foreach ($related_row as $column => $value) {
                                    $related_obj->{$column} = $value; //fill object with new values
                                }
                                if ($create) { //create new record
                                    if (! $related_obj->save()) {
                                        $error .= $related_obj->error->string;
                                    } else {
                                        $created_ids[$f][] = $related_obj->id;//add id to returned array 
                                    }
                                }
                                $error_classes_to_check[] = $f;
                                $new_related_objects[$f][] = $related_obj;
                            }
                            //delete not provided objects
                            if (!empty($related_ids)) {
                                $old_related_obj = $object->{$f}->where_not_in('id',$related_ids)->select('id')->get();
                            } else {
                                $old_related_obj = $object->{$f}->select('id')->get();
                            }
                            if ($has_join_table) {
                                $object->delete($old_related_obj);
                            } else {
                                $old_related_obj->delete_all();
                            }
                        }

                    } else {

                        if (empty($ids)) {
                            $object->delete( array($f => $object->{$f}->select('id')->get()->all ) );
                        } else {

                            $rels = new $c();
                            // Otherwise, get the new ones...
                            $rels->where_in('id', $ids)->select('id')->get();
                            // Store them...
                            $new_related_objects[$f] = $rels->all;
                            // And delete any old ones that do not exist.
                            $old_rels = $object->{$f}->where_not_in('id', $ids)->select('id')->get();
                            $object->delete( array($f => $old_rels->all) );
                        }
                    }
                }
                else
                {
                    // Otherwise, if the $data was set, store it...
                    if(isset($data[$f]))
                    {
                        $v = $data[$f];
                    }
                    else
                    {
                        // Or assume it was an unchecked checkbox, and clear it.
                        $v = FALSE;
                    }
                    $object->{$f} = $v;
                }
            }
        }
        if($save) //save
        {
            if ($sub_array) {
                $object->save_from = new stdClass(); //add properties to object
                $object->save_from->error = $error;
                $object->save_from->created_ids = $created_ids;
                if (empty($error)) { //if no error while creating new related objects
                    $object->save($new_related_objects);
                    $error .= $object->error->string;
                    foreach ($error_classes_to_check as $error_class) {
                        $error .= $object->{$error_class}->error->string; //add errors from updated related objects
                    }
                    $object->save_from->error = $error;
                    if (empty($error)) {
                        return true;
                    } else {
                        //remove created objects, because some of relation couldn't be saved
                        if (isset($created_ids) && !empty($created_ids) && is_array($created_ids)) {
                            foreach ($created_ids as $related_class_name => $related_ids) {
                                $class = get_class($object->{$related_class_name});
                                foreach ($related_ids as $created_id) {
                                    $related_class = new $class($created_id);
                                    $related_class->delete();
                                }
                            }
                        }
                        return false;
                    }
                } else {
                    //remove created objects, because some of relation couldn't be saved
                    if (isset($created_ids) && !empty($created_ids) && is_array($created_ids)) {
                        foreach ($created_ids as $related_class_name => $related_ids) {
                            $class = get_class($object->{$related_class_name});
                            foreach ($related_ids as $created_id) {
                                $related_class = new $class($created_id);
                                $related_class->delete();
                            }
                        }
                    }
                    return false;
                }

            }
            return $object->save($new_related_objects);
        }
        else
        {
            // return new objects
            return $new_related_objects;
        }
    }

}
