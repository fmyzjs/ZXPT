<?php   
 $memcache = new Memcache; //����һ��memcache����   
 $memcache->connect('localhost', 11211) or die ("Could not connect"); //����Memcached������   
$memcache->set('key', 'test'); //����һ���������ڴ��У�������key ֵ��test   
$get_value = $memcache->get('key'); //���ڴ���ȡ��key��ֵ   
echo $get_value;