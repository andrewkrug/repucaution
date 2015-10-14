<?php

class Crm_directory_task extends CLI_controller
{

    /**
     * Grab results for single crm directory
     *
     * @param $directory_array (array) - crm directory model arrayed
     *
     * @throws Exception
     */
    public function grabber($directory_array)
    {

        try {

            $directory_id = Arr::get($directory_array, 'id');
            $directory = new Crm_directory($directory_id);

            $error_info = 'mkwid: ' . Arr::get($directory_array, 'social', 'no soc') . '/' . $directory_id;

            if (!$directory->exists()) {
                throw new Exception($error_info . ' doesn\'t exist.');
            }

            if ($directory->is_deleted) {
                throw new Exception($error_info . ' is set for deletion.');
            }

            if (!$directory->user_id) {
                throw new Exception($error_info . ' has no user id.');
            }

            $user = new User($directory->user_id);
            if (!$user->exists()) {
                throw new Exception($error_info . ' has no user');
            }

            $social = Arr::get($directory_array, 'social');
            if (is_null($social)) {
                throw new Exception($error_info . ' invalid social');
            }

            $user_socials = Access_token::inst()->get_crm_user_socials($directory->user_id, $directory->profile_id);

            if (!in_array($social, $user_socials)) {
                throw new Exception($error_info . ' invalid social');
            }

            $this->load->library('crmer');
            $crmer = Crmer::factory($user->id, $directory->profile_id);


            if ($social === 'facebook') {
                $data = $crmer->getCrmPosts($directory_array);
            } else if ($social === 'twitter') {
                $data = $crmer->getCrmTweets($directory_array);
            } else if ($social === 'instagram') {
                $data = $crmer->getCrmActivities($directory_array);
            } else {
                $data = array();
            }

            if (!is_array($data)) {
                throw new Exception($error_info . ' no results for activities, not an array. mkwid: ');
            }

            foreach ($data as $original_id => $row) {

                $activity = new Crm_directory_activity();
                $activity->where(array(
                    'crm_directory_id' => $directory_array['id'],
                    'original_id'        => $original_id,
                ))->get(1);

                $activity->social = $social;
                $activity->original_id = Arr::get($row, 'original_id');
                $activity->created_at = Arr::get($row, 'created_at');

                $message = Arr::get($row, 'message');
                $trimMessage = (strlen($message)>4000) ? substr($message, 0, 4000) : $message;
                $activity->message = $trimMessage;

                $activity->creator_id = Arr::get($row, 'creator_id');
                $activity->creator_name = Arr::get($row, 'creator_name');
                $activity->creator_image_url = Arr::get($row, 'creator_image_url');
                $activity->other_fields = serialize(Arr::get($row, 'other_fields', array()));
                $activity->source = Arr::get($row, 'source');

                $relations = array(
                    'crm_directory' => $directory,
                );

                $saved = $activity->save($relations);


                if (!$saved) {
                    log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Activity not saved for mkwid: ' . $error_info . ' grabbed: ' . $activity->error->string);
                }
            }

            // get socials that were already grabbed
            $grabbed_socials = $directory->get_grabbed_socials_as_array();

            if (!in_array($social, $grabbed_socials)) {

                $grabbed_socials[] = $social;
                $now = date('U');

                $directory->grabbed_socials = implode(',', $grabbed_socials);
                $directory->grabbed_at = $now;

                $saved = $directory->save();

                if (!$saved) {
                    log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Activity not saved for mkwid: ' . $error_info . ' grabbed: ' . $activity->error->string);
                }
            }

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Activities for mkwid: ' . $error_info . ' grabbed');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }

    }

    /**
     * Remove single directory with all collected activities
     *
     * @param $directory_array (array) - crm directory model arrayed
     *
     * @throws Exception
     */
    public function remove_deleted($directory_array)
    {
        try {

            $directory_id = isset($directory_array['id']) ? $directory_array['id'] : null;
            $directory = new Crm_directory($directory_id);

            if (!$directory->exists()) {
                throw new Exception('mkwid: ' . $directory_id . ' doesn\'t exist.');
            }

            $directory_activities = Crm_directory_activity::inst()->get_by_directory_id($directory_id);
            $directory_activities->delete_all();

            $directory->delete();

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Crm directory and activities for mkwid: ' . $directory_array['id'] . ' deleted');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }
    }

    public function remove_unrelated()
    {
        try {

            $limit = 500;

            $mentions = Crm_directory::inst()->where('user_id IS NULL')->get($limit)->delete_all();

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Unrelated crm directories removed');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }
    }


}