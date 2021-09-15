<?php

use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;

class LandingPage extends Page {

	private static $db = array(
		'LeftCopy' => 'Text',
		'RightCopy' => 'Text',
		'LeftCopy2' => 'Text'
	);
	private static $has_one = array(
    	'HeroBanner' => Image::class,
    	'LeftImage' => Image::class,
    	'RightImage' => Image::class
	);

    private static $owns = [
        'HeroBanner', 'LeftImage', 'RightImage'
    ];

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->HeroBanner()->exists() && !$this->HeroBanner()->isPublished()) {
            $this->HeroBanner()->doPublish();
        }

        if ($this->LeftImage()->exists() && !$this->LeftImage()->isPublished()) {
            $this->LeftImage()->doPublish();
        }

        if ($this->RightImage()->exists() && !$this->RightImage()->isPublished()) {
            $this->RightImage()->doPublish();
        }
    }

    public function getCMSFields() {
	    $fields = parent::getCMSFields();
	     // ...
		$fields->addFieldToTab('Root.Main', new TextareaField('LeftCopy'), 'Content');
		$fields->addFieldToTab('Root.Main', new TextareaField('RightCopy'), 'Content');
		$fields->addFieldToTab('Root.Main', new TextareaField('LeftCopy2'), 'Content');


	    $fields->addFieldToTab('Root.Attachments', UploadField::create('HeroBanner'));
	    $fields->addFieldToTab('Root.Attachments', UploadField::create('LeftImage'));
	    $fields->addFieldToTab('Root.Attachments', UploadField::create('RightImage'));


	    return $fields;
    }
}