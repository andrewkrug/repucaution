<?php
/**
 * User: Dred
 * Date: 27.02.13
 * Time: 14:13
 */
class Reviews_task extends CLI_controller
{

    public function add($args=array()){
        log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Reviews task started');

        $all_dir_users = Directory_User::get_all();

        $acc = $this->getAAC();

        foreach($all_dir_users as $_dir_user){
            //TODO - optimize this
            $user = new User($_dir_user->user_id);

            if (!$user->exists()) {
                continue;
            }

            $acc->setUser($user);

            if (!$acc->isGrantedPlan('reviews_monitoring')) {
                continue;
            }

            $args = $_dir_user->to_array();

            $this->jobQueue->addJob('tasks/reviews_task/grabber',  $args);
        }

    }

    public function addByUser(array $user_id_array){
        $all_dir_users = Directory_User::get_by_user($user_id_array[0]);

        foreach($all_dir_users as $_dir_user){
            $args = $_dir_user->to_array();
            $this->jobQueue->addJob('tasks/reviews_task/grabber',  $args);
        }
    }

    public function grabber(array $directory_user) {

        log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Reviews grabber');
        try {

            $directoryUser = new Directory_User($directory_user['id']);
            if(!$directoryUser->exists()) {
                throw new Exception('Directory_User id:' . $directory_user['id'] . ' doesn\'t exist');
            }
            $directory = $directoryUser->directory->get();
            if(!$directory->exists()) {
                throw new Exception('Directory id:' . $directory_user['directory_id'] . ' doesn\'t exist');
            }
            if(!$directory->status){
                throw new Exception('Directory id:' . $directory_user['directory_id'] . ' is disabled');
            }

            $link = !empty($directory_user['additional']) ? $directory_user['additional'] : $directory_user['link'];

            log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Try to grabb - '.$directory->name);

            $aac = $this->getAAC();

            $user = new User($directory_user['user_id']);

            if (!$user->exists()) {
                return;
            }

            $aac->setUser($user);

            $directory_parcer = Directory_Parser::factory($directory->type)->set_url( $link );
            $reviews = $directory_parcer->get_reviews();

            /**
             * Store additional data to
             */
            if ($directory_parcer instanceof Directory_Interface_UserStorage) {
                $directoryUser
                    ->setAdditional($directory_parcer->getDataToStore())
                    ->save()
                ;
            }

        }
        catch(Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Reviews: ' . $e->getMessage());
            throw $e;
        }

        //$today_midnight = strtotime('-7 day midnight');
        $today_midnight = strtotime('-14 day midnight');


        if (is_array($reviews) && !empty($reviews)) {
            foreach($reviews as $_review) {

                $review_model = new Review();
                $review_model->from_array($_review);
                $review_model->user_id = $directory_user['user_id'];
                $review_model->directory_id = $directory_user['directory_id'];
                $review_model->profile_id = $directory_user['profile_id'];
                $review_model->posted_date = date('Y-m-d', $_review['posted']);
                $review_model->save();

                log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Review saved');

            }
        }
    }

}