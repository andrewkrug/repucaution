<?php

class Activity_task extends CLI_controller {

    /**
     * Grab activities(only Linkedin) for every users
     * 
     * 
     * @throws Exception
     */
    public function grabber() {

       $this->load->library('activitioner');
        try{
            $user = new User();
            $users = $user->get()->all;
            $socials = array(
//                'linkedin'
            );
            if (empty($socials)) {
                return;
            }

            $aac = $this->getAAC();

            foreach($users as $u){

                $aac->setUser($u);

                if (!$aac->isGrantedPlan('social_activity')) {
                    continue;
                }

                $act = Activitioner::factory($u->id);
                foreach($socials as $social){
                    if($social == 'linkedin'){
                        $act->getLinkedinUpdates();
                    }
                     log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Activities for '.$social.' mkwid: grabbed');
                }
               
            }
            
        } catch (Exception $e) {
            
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
           
        }

    }



}