<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'ForumController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;



$route['user'] = 'UserController/index';
$route['user/(:any)'] = 'UserController/index/$1';
$route['user/(:any)/edit'] = 'UserController/edit/$1';
$route['user/(:any)/delete'] = 'UserController/delete/$1';
$route['register'] = 'UserController/register';
$route['register'] = 'UserController/register';
$route['login'] = 'UserController/login';
$route['UserController.php/login'] = 'UserController/login';
$route['logout'] = 'UserController/logout';
$route['email_validation'] = 'UserController/email_validation';
$route['feed']='UserController/feed';
$route['vote/(:any)'] = 'UserController/voteUp';



$route['create_forum'] = 'ForumController/createForum';
$route['(:any)/create_topic'] = 'ForumController/createTopic/$1';
$route['forum/(:any)'] = 'ForumController/index/$1';
$route['forum/(:any)/(:any)'] = 'ForumController/topic/$1/$2';
$route['forum/(:any)/(:any)/reply'] = 'ForumController/createPost/$1/$2';
$route['forum/(:any)/(:any)/reply/(:num)'] = 'ForumController/createPost/$1/$2';
$route['forum/delete/(:any)/(:num)']= 'ForumController/delete/$1/$2';
$route['report/(:any)/(:num)']= 'ForumController/report/$1/$2';



$route['admin'] = 'AdminController';
$route['admin/users'] = 'AdminController/users';
$route['admin/edit_user'] = 'AdminController/editUser';
$route['admin/edit_user/(:any)'] = 'AdminController/editUser/$1';
$route['admin/delete_user/(:any)'] = 'AdminController/deleteUser/$1';
$route['admin/forums_and_topics'] = 'AdminController/forums_and_topics';
$route['admin/contactos'] = 'AdminController/contactos';
$route['admin/preview/(:any)/(:num)/(:num)'] = 'AdminController/contactos';
$route['admin/delete/forum/(:any)']='AdminController/deleteForum/$1';
$route['admin/preview/forum_topics/(:any)']='AdminController/forums_and_topics';
$route['admin/preview/forum_topics/(:num)/posts/(:num)']='AdminController/forums_and_topics';
$route['admin/delete/topic/(:num)']='AdminController/deleteTopic/$1';
$route['admin/delete/post/(:num)']='AdminController/deletePost/$1';
$route['admin/answer/(:num)']='AdminController/answer';


$route['info'] = 'ContactosController';
$route['contacto/contactar'] = 'ContactosController/Contactar';
