<?php

/**
 * Settings for file uploading
 */
$config['max_video_file_size'] = 1024 * 1024 * 1024 * 5;
$config['max_image_file_size'] = 1024 * 1024 * 2;
$config['max_doc_file_size'] = 1024 * 1024 * 10;
$config['default_max_file_size'] = 1024 * 1024 * 10;

$config['video_file_types'] = array(
                                    'mime-types' => array(
                                                          'video/avi',
                                                          'video/quicktime',
                                                          'video/mpeg',
                                                          'video/mp4',
                                                          'video/x-ms-asf',
                                                          'video/webm',
                                                          'video/3gpp',
                                                          'video/ogg',
                                                          'video/x-flv',
                                                          'video/avi',
                                                          'video/msvideo',
                                                          'video/x-msvideo',
                                                          'video/x-ms-wmv',
                                                          'application/octet-stream',
                                                          'application/x-dosexec'
                                                    ),
                                    'extensions' => array(
                                                          'avi',
                                                          'mov',
                                                          '3gp',
                                                          'mpeg',
                                                          'mp4',
                                                          'webm',
                                                          'wmv',
                                                          'flv',
                                                          'ogg'
                                                         )

);
$config['doc_file_types'] = array(
                                 'mime-types' => array(
                                                      'application/psd',
                                                      'application/pdf',
                                                      'application/msword',
                                                      'text/plain',
                                                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                      'application/octet-stream'
                                                      ),
                                'extensions' => array(
                                                      'psd',
                                                      'pdf',
                                                      'doc',
                                                      'docx'
                                                    )
);
$config['image_file_types'] = array(
                                    'mime-types' => array(
                                                          'image/png',
                                                          'image/jpeg',
                                                          'image/jpg',
                                                          'image/gif',
                                                          'application/octet-stream'
                                                          ),
                                    'extensions' => array(
                                                          'png',
                                                          'jpg',
                                                          'jpeg',
                                                          'gif'
                                                          )
);
$config['default_file_types'] = array(
                                      'mime-types' => array(
                                                            'application/psd',
                                                            'application/pdf',
                                                            'image/png',
                                                            'image/jpeg',
                                                            'image/jpg',
                                                            'application/octet-stream'
                                                            ),
                                      'extensions' => array(
                                                           'png',
                                                           'jpg',
                                                           'jpeg',
                                                           'gif',
                                                           'psd',
                                                           'pdf'
                                                          )
);
$config['temp_upload_file_path'] = '/tmp_uploads';
$config['dest_upload_file_path'] = '/public/uploads/files';
$config['dest_upload_video_path'] = '/public/uploads/files/video';
$config['dest_upload_attachments_path'] = '/public/uploads/files/attachments';
$config['dest_upload_images_path'] = '/public/uploads/files/images';