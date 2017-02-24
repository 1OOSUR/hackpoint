<?php

/**
* Readme resource type
* @author valentin carruesco
* @category Core
* @license copyright
*/


class Readme{
	
	
	public static function infos(){
		return array(
			'uid' => 'readme',
			'label' => 'README',
			'extension' => array('md'),
			'codemirror' => array(
				'smartIndent' => false,
				'readOnly' =>  false
			)
		);
	}
	
	public static function fromFileImport($file,$sketch,$type){
		$resource = new Resource();
		$resource->sketch = $sketch->id;
		$stream = file_get_contents($file['tmp_name']);
		$resource->label = $file['name'];
		$resource->type = $type;
		$resource->content = file_get_contents($file['tmp_name']);
		$enc = mb_detect_encoding($resource->content,"UTF-8, ISO-8859-1, GBK");
		if($enc!='UTF-8')
			$resource->content = iconv($enc,"utf-8",$resource->content); 	
		$resource->save();
	}
	
	public static function fromImport($res,$sketch){
		global $myUser;
		$resource = new Resource();
		$resource->fromArray($res);
		$resource->id = null;
		$resource->sketch = $sketch->id;
		$stream = '';
		if(is_string($resource->content))
			$resource->content = htmlspecialchars_decode($resource->content);
		$resource->save();
	}
	
	
	public static function toExport($resource){
		$resource = $resource->toArray();
		$resource['content'] = htmlspecialchars(SKETCH_PATH.$resource['content']);
		return $resource;
	}
	
	public static function toHtml($resource,$sketch){
		global $myUser;
		$response = $resource->toArray();
		$response['content'] = '<textarea>'.$response['content'].'</textarea>';
		$response['code'] = self::infos()['codemirror'];
		if($myUser->id != $sketch->owner) $response['code']['readOnly'] = true;
		return $response;
	}
	
	public static function toFileStream($resource){
		return (object) array('name'=>slugify($resource->label).'.'.self::infos()['extension'][0],'content'=>html_entity_decode($resource->content));
	}
	
}

?>