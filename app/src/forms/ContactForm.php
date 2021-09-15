<?php
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\Form;
use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Environment;

class ContactForm extends Form
{

    /**
     * Our constructor only requires the controller and the name of the form
     * method. We'll create the fields and actions in here.
     *
     */
    public function __construct($controller, $name) 
    {
        $fields = new FieldList(
                new TextField('Name'),
                new EmailField('Email'),
                new TextField('Company')
            );

        $actions = new FieldList(
            new FormAction('submit', 'Submit')
        );

        $required = new RequiredFields([
            'Name', 'Email', 'Company'
        ]);

        parent::__construct($controller, $name, $fields, $actions, $required);
    }


    /**
     *
     * Action that handle the form submission
     * send email and save record to database
     *
     * @params: $data array
     * @params: $form this form object
     *
     * @return: redirect back with a flash message
     *
     */
    public function submit($data, $form)
    {
        //basic validation
        if (empty($data['Email'])) {
            $form->sessionMessage('Email should not be empty!', 'bad');
            return $this->controller->redirectBack();
        }

        if (!filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
            $form->sessionMessage('Please check email format!', 'bad');
            return $this->controller->redirectBack();
        }

        if (empty($data['Name'])) {
            $form->sessionMessage('Name should not be empty!', 'bad');
            return $this->controller->redirectBack();
        }

        if (empty($data['Name'])) {
            $form->sessionMessage('Company should not be empty!', 'bad');
            return $this->controller->redirectBack();
        }

        // prevent spam
        $email = Convert::raw2sql($data['Email']);

        $existing = DataObject::get_one("Contact", "Email = '$email'");

        if ($existing) {
            $form->sessionMessage('That email already sent an enquiry!', 'bad');
            return $this->controller->redirectBack();
        }

        // save to DB
        $contact = Contact::create();
        $form->saveInto($contact);

        if (!$contact->write()) {
            $form->sessionMessage('Failed to save submission - Please try again later!', 'bad');
            return $this->controller->redirectBack();
        }

        // get email to in .env file, fallback to personal email
        $toEmail = Environment::getEnv('SS_EMAIL_TO') ?? 'tuananh191194@gmail.com';

        // sanitise data
        $name = htmlentities($data["Name"]);
        $fromEmail = htmlentities($data['Email']);
        $company = htmlentities($data['Company']);

        // send email
        $email = new Email();

        $email->setTo($toEmail);
        $email->setFrom($fromEmail);
        $email->setSubject("Contact Message from {$name}");

        $messageBody = "
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Company:</strong> {$company}</p>
        ";

        $email->setBody($messageBody);

        if (!$email->send()) {
            $form->sessionMessage('Failed to send email - Please try again later!', 'bad');
            return $this->controller->redirectBack();
        }

        // looks all good - redirect
        $form->sessionMessage('Thanks for your contact submission!', 'good');

        return $this->controller->redirectBack();
    }
}