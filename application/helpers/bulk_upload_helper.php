<?php
/**
 * Created by PhpStorm.
 * User: beer
 * Date: 30.6.15
 * Time: 16.03
 */

class Bulk_upload {

    const SOCIALS_POSITION = 0;
    const STATUS_POSITION = 1;
    const YEAR_POSITION =2;
    const MONTH_POSITION =3;
    const DAY_POSITION =4;
    const HOUR_POSITION =5;
    const MINUTE_POSITION =6;
    const IMAGE_POSITION =7;
    const LINK_POSITION =8;

    /**
     * @param $csv_filename
     * @param $user_id
     * @param $profile_id
     *
     * @return array
     */
    static function getScheduledPostsArray($csv_filename, $user_id, $profile_id) {
        $error = '';
        $posts = [];
        $row = 1;
        if (($handle = fopen($csv_filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $post = [
                    'posting_type' => 'schedule',
                    'post_to_groups' => [$profile_id],
                    'timezone' => User_timezone::get_user_timezone($user_id)
                ];

                //VALIDATE SOCIALS. MUST BE IN FORMT social\social\social.
                if(isset($data[self::SOCIALS_POSITION])) {
                    $validate_socials = self::validateSocials($data[self::SOCIALS_POSITION]);
                    if($validate_socials['success']) {
                        $post['post_to_socials'] = $validate_socials['data'];
                    } else {
                        $error = self::getErrorMessage(
                            $row,
                            self::SOCIALS_POSITION,
                            $validate_socials['error']
                        );
                        break;
                    }
                } else {
                    $error = self::getErrorMessage(
                        $row,
                        self::SOCIALS_POSITION,
                        'Can`t find socials'
                    );
                    break;
                }

                //VALIDATE SCHEDULED DATE
                if( !isset($data[self::YEAR_POSITION]) ||
                    !isset($data[self::MONTH_POSITION]) ||
                    !isset($data[self::DAY_POSITION]) ||
                    !isset($data[self::HOUR_POSITION]) ||
                    !isset($data[self::MINUTE_POSITION])) {
                    $error = self::getErrorMessage(
                        $row,
                        self::YEAR_POSITION.'-'.self::MINUTE_POSITION,
                        'Wrong scheduled date'
                    );
                    break;
                } else {
                    $validate_scheduled_date = self::validateScheduleDate(
                        $data[self::YEAR_POSITION],
                        $data[self::MONTH_POSITION],
                        $data[self::DAY_POSITION],
                        $data[self::HOUR_POSITION],
                        $data[self::MINUTE_POSITION]
                    );
                    if($validate_scheduled_date['success']) {
                        $post['schedule_date'] = $validate_scheduled_date['data'];
                    } else {
                        $error = self::getErrorMessage(
                            $row,
                            self::YEAR_POSITION.'-'.self::MINUTE_POSITION,
                            $validate_scheduled_date['error']
                        );
                        break;
                    }
                }

                //VALIDATE IMAGE
                if(isset($data[self::IMAGE_POSITION]) && !empty($data[self::IMAGE_POSITION])) {
                    $validate_image = self::validateImage($data[self::IMAGE_POSITION], $user_id);
                    if($validate_image['success']) {
                        $post['image_name'] = $validate_image['data'];
                    } else {
                        $error = self::getErrorMessage(
                            $row,
                            self::IMAGE_POSITION,
                            $validate_image['error']
                        );
                        break;
                    }
                }

                //VALIDATE LINK
                if(isset($data[self::LINK_POSITION]) && !empty($data[self::LINK_POSITION])) {
                    if (filter_var($data[self::LINK_POSITION], FILTER_VALIDATE_URL) === false ||
                        strstr($data[self::LINK_POSITION], '.') === false
                    ) {
                        $error = self::getErrorMessage(
                            $row,
                            self::LINK_POSITION,
                            'The URL is invalid. (Example: http://google.com)'
                        );
                        break;
                    } else {
                        $post['url'] = $data[self::LINK_POSITION];
                    }
                }

                //VALIDATE STATUS
                if(isset($data[self::STATUS_POSITION])) {
                    $validate_status = self::validateStatus($data[self::STATUS_POSITION], $post);
                    if($validate_status['success']) {
                        $post['description'] = $validate_status['data'];
                    } else {
                        $error = self::getErrorMessage(
                            $row,
                            self::STATUS_POSITION,
                            $validate_status['error']
                        );
                        break;
                    }
                } else {
                    $error = self::getErrorMessage(
                        $row,
                        self::STATUS_POSITION,
                        'Status in required.'
                    );
                    break;
                }

                $row++;
                $posts[] = $post;
            }
            fclose($handle);
        }
        if($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        } else {
            return [
                'success' => true,
                'data' => $posts
            ];
        }
    }

    /**
     * @param $row
     * @param $col
     * @param $message
     *
     * @return string
     */
    static function getErrorMessage($row, $col, $message) {
        return 'Error in '.$row.' row and '.$col.' column with message: '.$message;
    }

    /**
     * @param $raw_socials
     *
     * @return array
     */
    static function validateSocials($raw_socials) {
        $available_socials = Social_post::$socials;
        $socials = preg_split('/\//', $raw_socials);
        $error = '';
        if($socials) {
            foreach($socials as $social) {
                if(!in_array($social, $available_socials)) {
                    $error = 'Wrong social`s. Now available only Twitter, Facebook and Linkedin.';
                }
            }
        } elseif(in_array($raw_socials, $available_socials)) {
           $socials = array($raw_socials);
        } else {
            $error = 'Socials column must look like: twitter/facebook/linkedin';
        }
        if($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        } else {
            return [
                'success' => true,
                'data' => $socials
            ];
        }
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $minute
     *
     * @return array
     */
    static function validateScheduleDate(
        $year,
        $month,
        $day,
        $hour,
        $minute
    ) {
        $now = new DateTime('now');
        $scheduled = new DateTime($year.'/'.$month.'/'.$day.' '.$hour.':'.$minute);
        $error = '';
        $data = '';
        if($scheduled) {
            $diff = $now->diff($scheduled, true);
            if(!$diff->invert && $diff->days) {
                $data = $scheduled->format('m/d/Y h:i A');
            } else {
                $error = 'Scheduled date must be more then '.$now->format('d/m/Y');
            }
        } else {
            $error = 'Wrong scheduled date.';
        }
        if($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        } else {
            return [
                'success' => true,
                'data' => $data
            ];
        }
    }

    /**
     * @param $link_to_image
     * @param $user_id
     *
     * @return array
     */
    static function validateImage($link_to_image, $user_id) {
        $error = '';
        $nameImage = '';
        $extensions = preg_split('/\./', $link_to_image);
        if($extensions) {
            $nameImage = time() . '.'.$extensions[count($extensions)-1];
            $path = dirname($_SERVER['SCRIPT_FILENAME']).'/public/uploads/'.$user_id.'/';
            if(!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            }
            $path.=$nameImage;
            if(!file_put_contents($path, file_get_contents($link_to_image))) {
                $error = 'Wrong image.';
            }
        } else {
            $error = 'Wrong image link.';
        }
        if($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        } else {
            return [
                'success' => true,
                'data' => $nameImage
            ];
        }
    }

    /**
     * @param $raw_status
     * @param $post
     *
     * @return array
     */
    static function validateStatus($raw_status, $post) {
        $error = '';
        if(!empty($raw_status)) {
            if(in_array('twitter', $post['post_to_socials'])) {
                $twitter_limit = array(
                    1 => 140,
                    2 => 117,
                    3 => 94
                );
                $input_category = 1;

                $file = (!empty($post['image_name']) || isset($post['file_name']) );
                $link = !empty($post['url']);

                if($file && $link){
                    $input_category = 3;
                }elseif($file || $link){
                    $input_category = 2;
                }
                if(mb_strlen($raw_status) > $twitter_limit[$input_category]){
                    $error = 'Post too long for Twitter. Please remove some text and try again';
                }
            }
            if(in_array('linkedin', $post['post_to_socials'])) {
                if(mb_strlen($raw_status) > 400){
                    if(!empty($error)) {
                        $error.="<br>";
                    }
                    $error .= 'Post too long for Linkedin. Please remove some text and try again';
                }
            }
        } else {
            $error = 'Status is required.';
        }
        if($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        } else {
            return [
                'success' => true,
                'data' => $raw_status
            ];
        }
    }
}