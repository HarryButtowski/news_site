<?php
/**
 * SlugBehavior class file.
 *
 * @author Aleksey Konovalov <avalak.box@gmail.com>
 * @link http://avalak.net/
 * @copyright Copyright &copy; 2010 
 * @license http://www.yiiframework.com/license/
 *
 * NOTE: Behavior use Google_Translate_API.php by @author gabe@fijiwebdesign.com
 */

/**
 * Google_Translate_API.php
 * 
 * Translating language with Google API
 * @author gabe@fijiwebdesign.com
 * @version $Id: google.translator.php 7 2009-08-20 09:30:43Z bucabay $
 * @license - Share-Alike 3.0 (http://creativecommons.org/licenses/by-sa/3.0/)
 * 
 * Google requires attribution for their Language API, please see: http://code.google.com/apis/ajaxlanguage/documentation/#Branding
 */

/*
 * Usage example:
			'SlugBehavior' => array(
				'class' => 'ext.aii.behaviors.SlugBehavior',
				'sourceAttribute' => 'title',
				'slugAttribute' => 'slug',
				'mode' => 'translate',
			),
 *
 */
class SlugBehavior extends CActiveRecordBehavior
{
	/**
	* @var mixed The name of the attribute to store the modification time.  Set to null to not
	* use a timstamp for the update attribute.  Defaults to 'update_time'
	*/
	const MODE_NUMBER = 'number'; // Dummy value
	const MODE_TRANSLIT = 'translit';
	const MODE_TRANSLATE = 'translate';
	const GOOGLE_API_PATH = 'ext.googleapi.Google_Translate_API';
	
	// source slug
	public $sourceAttribute = 'title';
	// result
	public $slugAttribute = 'slug';
	// api path
	public $googleApiPath = self::GOOGLE_API_PATH;
	//mode: translit / translate
	public $mode = self::MODE_TRANSLIT;

	public $connectionId = 'db';

	/**
	 * Smiles
	 *
	 * @var array
	 */
	public $smileList = array(
		')' => '',
		':)' => '',
		'=)' => '',
		':' => '',
		'(' => '',
		'(:' => '',
		'(=' => '',
		':D' => '',
		':P' => '',
		':3' => '',
	);
	

	/**
	 * Данные для транслита
	 *
	 * @var unknown_type
	 */
	protected $replaceList = array(
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'g', 'з' => 'z',
		'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
		'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'ы' => 'i', 'э' => 'e', 'А' => 'A',
		'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'G', 'З' => 'Z', 'И' => 'I',
		'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
		'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Ы' => 'I', 'Э' => 'E', 'ё' => "yo", 'х' => "h",
		'ц' => "ts", 'ч' => "ch", 'ш' => "sh", 'щ' => "shch", 'ъ' => "", 'ь' => "", 'ю' => "yu", 'я' => "ya",
		'Ё' => "YO", 'Х' => "H", 'Ц' => "TS", 'Ч' => "CH", 'Ш' => "SH", 'Щ' => "SHCH", 'Ъ' => "", 'Ь' => "",
		'Ю' => "YU", 'Я' => "YA",
		' ' => '-',
	);
	
	/**
	* Responds to {@link CModel::onBeforeSave} event.
	* Sets the values of the creation or modified attributes as configured
	* 
	* @param CModelEvent event parameter
	*/
	public function beforeSave($event)
	{
		if($this->getOwner()->isNewRecord)
			if($this->mode == self::MODE_TRANSLIT)
				$this->getOwner()->{$this->slugAttribute} = $this->convertToSlug($this->getOwner()->{$this->sourceAttribute});
			else if($this->mode == self::MODE_TRANSLATE)
				$this->getOwner()->{$this->slugAttribute} = $this->translateSlug($this->getOwner()->{$this->sourceAttribute});
	}

	public function afterSave($event)
	{
		// add "-$id" to slug string to prevent collision
		if($this->getOwner()->isNewRecord)
		{
			$this->getOwner()->{$this->slugAttribute} = $this->getOwner()->{$this->slugAttribute}."-"
                                .$this->getOwner()->id;
			$this->getOwner()->isNewRecord = false;
			$this->getOwner()->update();
		}
	}

	/*
	 * Get translited 'slug'
	 * @param string title
	 * @return string slug
	 */
	protected function convertToSlug($source)
	{
		preg_match_all("/([\d\wа-яА-ЯёЁ ]+)/u", $source, $tmp);
		$preparedString = '';
		
		foreach($tmp[1] as $part)
			$preparedString .= $part;
			
		if(isset($tmp[1][0]))
			return strtolower(strtr(strtr(trim($preparedString), $this->replaceList), $this->smileList));
			
		return $source;
	}
	
	/*
	 * Get translated 'slug'
	 * @param string title
	 * @return string slug
	 */
	protected function translateSlug($source)
	{
		Yii::import($this->googleApiPath);
		$translated = Google_Translate_API::translate(strtr($source, $this->smileList));
		
		if(isset($translated))
			return $this->convertToSlug($translated);
			
		return $source;
	}
}
