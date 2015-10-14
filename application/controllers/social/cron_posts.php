<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_posts extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('cron_posts', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('cron_posts', $this->language)
        ]);
    }

    public function index() {
        $data = [];
        $days = Cron_day::inst()->get()->all_to_array(['day']);
        foreach($days as $day) {
            $data[$day['day']] = [];
        }
        $cron_posts = Social_post_cron::inst()->where(
            [
                'profile_id' => $this->profile->id,
                'user_id' => $this->c_user->id
            ]
        )->get();
        $utc_timezone = new DateTimeZone('UTC');
        /** @var Social_post_cron $cron_post */
        foreach($cron_posts as $cron_post) {
            $cron_days = $cron_post->cron_day->get();
            foreach($cron_days as $cron_day) {
                $time_in_utc = $cron_post->getTimeInUtc();
                foreach($time_in_utc as $time) {
                    $timezoned_time = new DateTime($time, $utc_timezone);
                    $timezoned_time->setTimezone(new DateTimeZone($cron_post->timezone));
                    if(!is_array($data[$cron_day->day][$timezoned_time->format(lang('time_without_minutes_format'))])) {
                        $data[$cron_day->day][$timezoned_time->format(lang('time_without_minutes_format'))] = [];
                    }
                    array_push(
                        $data[$cron_day->day][$timezoned_time->format(lang('time_without_minutes_format'))],
                        [
                            'post' => $cron_post,
                            'post_time' => $timezoned_time->format(lang('time_format'))
                        ]
                    );
                }
                ksort($data[$cron_day->day]);
            }
        }
        CssJs::getInst()->c_js('social/cron_post', 'index');
        $this->template->set('data', $data);
        $this->template->render();
    }

    public function delete( $post_id ) {
        if(Social_post_cron::inst($post_id)->delete()) {
            $this->addFlash(lang('deleting_success'), 'success');
        } else {
            $this->addFlash(lang('deleting_error'));
        }
        redirect('social/cron_posts');
    }

    public function getCronPostSchedule() {
        if( $this->template->is_ajax() ) {
            $failure_answer = [
                'success' => false,
                'message' => lang('cron_schedule_error')
            ];
            $id = $post = $this->input->post('id');
            if($id) {
                $cron_post = Social_post_cron::inst($id);
                if($cron_post->exists()) {
                    $answer = [
                        'success' => true,
                        'data' => [
                            'time' => $cron_post->getTimeInTimezone(),
                            'days' => $cron_post->getDays()
                        ]
                    ];
                    echo json_encode($answer);
                } else {
                    echo json_encode($failure_answer);
                }

            } else {
                echo json_encode($failure_answer);
            }
        }
        exit();
    }

    public function saveCronPostSchedule() {
        if( $this->template->is_ajax() ) {
            $failure_answer = [
                'success' => false,
                'message' => lang('saving_error')
            ];
            $post = $post = $this->input->post();
            if($post) {
                $cron_post = Social_post_cron::inst($post['id']);
                if($cron_post->exists()) {
                    $cron_post->setTimeInUtc($post['cron_schedule_time'], $cron_post->timezone);
                    $days = Cron_day::inst()->where_in('day', $post['cron_day'])->get();
                    $cron_post->delete($cron_post->cron_day->get()->all, 'cron_day');
                    if($cron_post->save($days->all, 'cron_day')) {
                        $answer = [
                            'success' => true,
                            'message' => lang('saving_success')
                        ];
                        echo json_encode($answer);
                    } else {
                        echo json_encode($failure_answer);
                    }

                } else {
                    echo json_encode($failure_answer);
                }
            } else {
                echo json_encode($failure_answer);
            }
        }
        exit();
    }

}