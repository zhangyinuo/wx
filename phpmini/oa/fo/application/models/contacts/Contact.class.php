<?php

/**
 * Contact class
 *
 * @author Carlos Palma <chonwil@gmail.com>, Diego Castiglioni <diego.castiglioni@fengoffice.com>
 */
class Contact extends BaseContact {
	
	protected $searchable_columns = array('name', 'first_name', 'surname', 'display_name');
	
	protected $is_read_markable = false;
	
	public $notify_myself;
	
	/**
	 * If contact is a company, cache the company users for subsequent calls
	 *
	 * @var array
	 */
	private $company_users = null;
	
	/**
	 * Cached is_account_owner value. Value is retrived on first requests
	 *
	 * @var boolean
	 */
	private $is_account_owner = null;
	
	private $company;
	
	/**
	 * Construct contact object
	 *
	 * @param void
	 * @return User
	 */
	function __construct() {
		parent::__construct ();
	} // __construct
	
	function getObjectTypeName() {
		if ($this->getIsCompany()) return 'company';
		else return 'contact';
	}
	
	/**
	 * Array of email accounts
	 *
	 * @var array
	 */
	protected $mail_accounts;
	function save(){
	   		parent::save();
	   		$sql = "DELETE FROM ".TABLE_PREFIX."searchable_objects
					WHERE rel_object_id = '".$this->getId()."' AND (column_name LIKE 'phone_number%'  OR column_name LIKE 'email_addres%' OR column_name LIKE 'web_url%' OR column_name LIKE 'im_value%' OR column_name LIKE 'address%')";
	   		DB::execute($sql);
	   		//save telephones on searchable_objects
	   		$telephones = $this->getAllPhones();
	   		$lengthTel = count($telephones);
	   		for ($i = 0; $i < $lengthTel; $i++) {
	   			$j=strval($i);
	   			$telephone = array_var($telephones, $j);
	   			if ($telephone instanceof ContactTelephone){
	   				$telephone =$telephone->getNumber();
	   			}else{
	   				continue;
	   			}
	   			$searchable_object = new SearchableObject();
	   			$searchable_object->setRelObjectId($this->getId());
	   			$searchable_object->setColumnName("phone_number".$j);
	   			$searchable_object->setContent($telephone);
	   			$searchable_object->save();	   			
	   		}
	   		//save emails on searchable_objects
	   		$emails = $this->getAllEmails();	   			   		
	   		$lengthEm = count($emails);
	   		for ($i = 0; $i < $lengthEm; $i++) {
	   			$j=strval($i);
	   			$email = array_var($emails, $j);
	   			if ($email instanceof ContactEmail){
	   				$email =$email->getEmailAddress();
	   			}else{
	   				continue;
	   			}
	   			if($email != ''){
		   			$searchable_object = new SearchableObject();
		   			$searchable_object->setRelObjectId($this->getId());
		   			$searchable_object->setColumnName("email_addres".$j);
		   			$searchable_object->setContent($email);
		   			$searchable_object->save();		   			
	   			}
	   		}
	   		//save web_pages on searchable_objects
	   		$web_pages = $this->getAllWebpages();
	   		$lengthWeb = count($web_pages);	   		
	   		for ($i = 0; $i < $lengthWeb; $i++) {
	   			$j=strval($i);
	   			$web_page = array_var($web_pages, $j);
	   			if ($web_page instanceof ContactWebpage){
	   				$web_page =$web_page->getUrl();
	   			}else{
	   				continue;
	   			}
	   			$searchable_object = new SearchableObject();
	   			$searchable_object->setRelObjectId($this->getId());
	   			$searchable_object->setColumnName("web_url".$j);
	   			$searchable_object->setContent($web_page);
	   			$searchable_object->save();	   			
	   		}  		
	   		//save im_values on searchable_objects
	   		$im_values = $this->getImValues();
	   		$lengthIm = count($im_values);
	   		for ($i = 0; $i < $lengthIm; $i++) {
	   			$j=strval($i);
	   			$im_value = array_var($im_values, $j);
	   			if ($im_value instanceof ContactImValue){
	   				$im_value =$im_value->getValue();
	   			}else{
	   				continue;
	   			}
	   			$searchable_object = new SearchableObject();
	   			$searchable_object->setRelObjectId($this->getId());
	   			$searchable_object->setColumnName("im_value".$j);
	   			$searchable_object->setContent($im_value);
	   			$searchable_object->save();	   			   			
	   		}
	   		//save addresses on searchable_objects
			$addresses = $this->getAllAddresses();
			$lengthAd = count($addresses);
			for ($i = 0; $i < $lengthAd; $i++) {
	   			$j=strval($i);
	   			$address = array_var($addresses, $j);
	   			if (!$address instanceof ContactAddress){
	   				continue;
	   			}
	   			$address = strval(array_var($addresses, $j)->getStreet())." ";
	   			$address .= strval(array_var($addresses, $j)->getCity())." ";
	   			$address .= strval(array_var($addresses, $j)->getState())." ";
	   			$address .= strval(array_var($addresses, $j)->getCountry())." ";
	   			$address .= strval(array_var($addresses, $j)->getZipCode());
	   			$searchable_object = new SearchableObject();
	   			$searchable_object->setRelObjectId($this->getId());
	   			$searchable_object->setColumnName("address".$j);
	   			$searchable_object->setContent($address);
	   			$searchable_object->save();
	   		}
	   		return true;
	}
	
	function hasMailAccounts(){
		if (Plugins::instance()->isActivePlugin('mail')) {
			if(is_null($this->mail_accounts))
				$this->mail_accounts = MailAccounts::getMailAccountsByUser(logged_user());
			return is_array($this->mail_accounts) && count($this->mail_accounts) > 0;
		}
		return false;
	}
	
	function hasReferences() {
		$id = $this->getId();
		
		// Check form linked objects
		$linked_obj_references_count = LinkedObjects::count("`created_by_id` = $id");
		if ($linked_obj_references_count > 0){
			return true;
		}
			
		// Check direct references
		$references = DB::executeAll("SELECT id FROM ".TABLE_PREFIX."objects WHERE `created_by_id` = $id OR `updated_by_id` = $id OR `trashed_by_id` = $id OR `archived_by_id` = $id limit 1");
		if (count($references) > 0){
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * @abstract Sets the user disabled, if it has no references in the system it is physically deleted
	 * @author Alvaro Torterola <alvaro.torterola@fengoffice.com>
	 */
	function disable($deleteInactive = true) {
		if (!$this->canDelete(logged_user())) {
			return false;	
		}
		
		if (parent::getUserType() != 0 && !$this->getDisabled()) {
			if (!$deleteInactive || $this->hasReferences() ) {
				$this->setDisabled(true);
				$this->save();
			} else {
				$this->do_delete();
			}
			return true;
		}
	}
	
	
	function do_delete() {
		$id = $this->getId();
		
		ContactAddresses::instance()->delete("`contact_id` = $id");
		ContactImValues::instance()->delete("`contact_id` = $id");
		ContactEmails::instance()->delete("`contact_id` = $id");
		ContactTelephones::instance()->delete("`contact_id` = $id");
		ContactWebpages::instance()->delete("`contact_id` = $id");
		ContactConfigOptionValues::instance()->delete("`contact_id` = $id");
		ContactPasswords::instance()->delete("`contact_id` = $id");
		
		ObjectSubscriptions::instance()->delete("`contact_id` = $id");
		ObjectReminders::instance()->delete("`contact_id` = $id");
		
		ContactPermissionGroups::instance()->delete("`contact_id` = $id");
		ContactMemberPermissions::instance()->delete("`permission_group_id` = " . $this->getPermissionGroupId());
		ContactDimensionPermissions::instance()->delete("`permission_group_id` = " . $this->getPermissionGroupId());
		SystemPermissions::instance()->delete("`permission_group_id` = " . $this->getPermissionGroupId());
		TabPanelPermissions::instance()->delete("`permission_group_id` = " . $this->getPermissionGroupId());
		
		$this->delete();
		$ret = null;
		Hook::fire("after_user_deleted", $this, $ret);
	}
	
	
	
	
	function modifyMemberValidations($member) {
		if ($member instanceof Member) {
			$member->add_skip_validation('uniqueness of parent - name');
		} else {
			if ($this->getId() > 0 && Plugins::instance()->isActivePlugin('core_dimensions')) {
				$dim = Dimensions::findByCode('feng_persons');
				if ($dim instanceof Dimension) {
					$m = Members::findByObjectId($this->getId(), $dim->getId());
					if ($m instanceof Member) {
						$m->add_skip_validation('uniqueness of parent - name');
					}
				}
			}
		}
	}
	
	
	
	
	// ---------------------------------------------------
	//  IMs
	// ---------------------------------------------------
	

	/**
	 * Return true if this contact have at least one IM address
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function hasImValue() {
		return ContactImValues::count('`contact_id` = ' . DB::escape ($this->getId()));
	} // hasImValue
	

	/**
	 * Return all IM values
	 *
	 * @access public
	 * @param void
	 * @return array
	 */
	function getImValues() {
		return ContactImValues::getByContact($this);
	} // getImValues
	

	/**
	 * Return value of specific IM. This function will return null if IM is not found
	 *
	 * @access public
	 * @param ImType $im_type
	 * @return string
	 */
	function getImValue(ImType $im_type) {
		$im_value = ContactImValues::findOne(array("conditions" => "`contact_id` = ".$this->getId()." AND `im_type_id` = ".$im_type->getId()));
		return $im_value instanceof ContactImValue && (trim($im_value->getValue()) != '') ? $im_value->getValue() : null;
	} // getImValue
	

	/**
	 * Return main IM value. If value was not found NULL is returned
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getMainImValue() {
		$main_im_type = $this->getMainImType();
		return $this->getImValue($main_im_type);
	} // getMainImValue
	

	/**
	 * Return main contact IM type. If there is no contact main IM type NULL is returned
	 *
	 * @access public
	 * @param void
	 * @return ImType
	 */
	function getMainImType() {
		return ContactImValues::getContactMainImType($this);
		
	} // getMainImType
	

	/**
	 * Clear all IM values
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function clearImValues() {
		return ContactImValues::instance()->clearByContact($this);
	} // clearImValues
	

	// ---------------------------------------------------
	//  Retrieve
	// ---------------------------------------------------
	

	/**
	 * Return display name for this account. If there is no display name set username will be used
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getDisplayName() {
		$display = parent::getDisplayName();
		return trim($display) == '' ? "".$this->getFirstName()." ".$this->getSurname() : $display;
	} // getDisplayName
	

	/**
	 * Return display name with last name first for this contact
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getReverseDisplayName() {
		if (parent::getSurname() != "")
			$display = parent::getSurname() . ", " . parent::getFirstName();
		else
			$display = parent::getFirstName();
		return trim ($display);
	} // getReverseDisplayName
	
	
	/**
	 * Returns the contact's company
	 *
	 * @access public
	 * @return Contact
	 */
	function getCompany() {
		if(is_null($this->company)) {
			$this->company = Contacts::findById($this->getCompanyId());
		}
		return $this->company;
	} // getCompany
	
	
	/**
	 * Returns true if contact is a user
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function isUser() {
		$type =  parent::getUserType();
		return $type != 0;
	} // isUser
	
	
	/**
	 * Returns true if is Owner company
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function isOwnerCompany() {
		return $this->getObjectId() == owner_company()->getId();
	} // isOwnerCompany

	
	/**
	 * Returns true if contact is a company
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function isCompany() {
		return parent::getIsCompany();
	} // isCompany
	

	/**
	 * Returns true if contact is an active user
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function isActiveUser() {
		return parent::getIsActiveUser();
	} // isActiveUser
	
	
	/**
	 * 
	 *
	 * @access public
	 * @param void
	 * @return string
	 * @deprecated
	 */
	 function getEmail($type=null) {
		if (is_null($type)){
			if ($this->getIsCompany()) {
				$type = 'work';
			} else {
				$type = $this->getUserType() > 0 ? 'user' : 'personal';
			}
		}
		$email_type_id = EmailTypes::getEmailTypeId($type);
		return ContactEmails::getContactMainEmail($this, $email_type_id);
	 } // getEmail
	 
	 
	 
	/**
	 * Return mail address for the contact.
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	 function getEmailAddress($type=null) {
	 	$contact_id = $this->getId();
	 	$type_condition =  ($type) ? "AND name = 'personal'" : "";
	 	
	 	$sql = "SELECT * FROM ".TABLE_PREFIX."contact_emails ce
				LEFT JOIN ".TABLE_PREFIX."email_types t
				ON ce.email_type_id = t.id
				WHERE TRIM(email_address) <> ''
				AND email_address IS NOT NULL
				AND contact_id = $contact_id
				$type_condition order by is_main desc LIMIT 1";
		if ($row = DB::executeOne($sql)) {
			return $row['email_address'];	
		}				
		return null;
	 } 
	
	 	 
	 function getContactEmails($type){
	 	$email_type_id = EmailTypes::getEmailTypeId($type);
		return ContactEmails::getContactEmails($this, $email_type_id);
	 }
	 

	/**
	 * Return  Address for this contact.
	 *
	 * @access public
	 * @param $typeId
	 * @param $main
	 * @return ContactTelephone
     * @author Seba
	 */
	function getAddress($type) {
		$address_type_id = AddressTypes::getAddressTypeId($type);
		return ContactAddresses::findOne(array('conditions' => array("`contact_id` = ? AND `address_type_id` = ?", $this->getId(), $address_type_id)));
	} // getMainPhone

	/**
	 * Return
	 *
	 * @access public
	 * @param $typeId
	 * @param $main
	 * @return string with formated address.
	 */
	function getStringAddress($type) {
		$address_type_id = AddressTypes::getAddressTypeId($type);
		$address = ContactAddresses::findOne(array('conditions' => array("`contact_id` = ? AND `address_type_id` = ?", $this->getId(), $address_type_id)));

		if (!$address instanceof ContactAddress) return "";

		$out = $address->getStreet();
		if($address->getCity() != '') {
			$out .= ' - ' . $address->getCity();
		}
		if($address->getState() != '') {
			$out .= ' - ' . $address->getState();
		}
		if($address->getCountry() != '') {
			$out .= ' - ' . $address->getCountryName();
		}
		return $out;
	} // getMainPhone
	
	
	/**
	 * Return personal fax phone for this contact.
	 *
	 * @access public
	 * @param void
	 * @return ContactTelephone
     * @author Seba
	 */
	function getPhone($type, $is_main = false, $check_is_main = true) {
		if ($check_is_main) {
			$is_main_cond = "`is_main` = ".($is_main ? 1 : 0);
		} else {
			$is_main_cond = "true";
		}
		$telephone_type_id = TelephoneTypes::getTelephoneTypeId($type);
		return ContactTelephones::findOne(array('conditions' => array("$is_main_cond AND `contact_id` = ? AND 
		`telephone_type_id` = ?", $this->getId(), $telephone_type_id)));
	} // getFaxPhone	
	
	function getAllPhones() {
		return ContactTelephones::findAll(array('conditions' => array("`contact_id` = ?" ,$this->getId())));
		
	} // getAllPhones
	
	function getAllEmails() {
		return ContactEmails::findAll(array('conditions' => array("`contact_id` = ?" ,$this->getId())));
	
	} // getAllEmails
	
	function getAllWebpages() {		
		return ContactWebpages::findAll(array('conditions' => array("`contact_id` = ?",	$this->getId())));
	} // getAllWebpages
	
	function getAllAddresses() {
		return ContactAddresses::findAll(array('conditions' => array("`contact_id` = ?", $this->getId())));
	} // getAllAddress
	/**
	 * Return personal fax phone for this contact.
	 *
	 * @access public
	 * @param void
	 * @return string
     * @author Seba
	 */
	function getPhoneNumber($type, $is_main = false, $check_is_main = true) {
		$telephone = $this->getPhone($type, $is_main, $check_is_main);
		$number = is_null($telephone)? '' : $telephone->getNumber();
		return $number;
	} // getPhoneNumber

	function getAllImValues() {
		$rows = DB::executeAll("SELECT i.value, t.name FROM ".TABLE_PREFIX."contact_im_values i INNER JOIN fo_im_types t ON i.im_type_id=t.id WHERE i.contact_id=".$this->getId());
		$res = array();
		foreach ($rows as $row) {
			$res[$row['name']] = $row['value'];
		}
		return $res;
	}
	
	/**
	 * Return first webpage for this contact.
	 *
	 * @access public
	 * @param void
	 * @return ContactWebpage
     * @author Seba
	 */
	function getWebpage($type) {
		$webpage_type_id = WebpageTypes::getWebpageTypeId($type);
		return ContactWebpages::findOne(array('conditions' => array("`contact_id` = ? AND `web_type_id` = ?", 
    		   $this->getId(), $webpage_type_id)));
	} // getWebpage	
	
	 
	/**
	 * Return first webpage URL for this contact.
	 *
	 * @access public
	 * @param void
	 * @return string
     * @author Seba
	 */
	function getWebpageUrl($type) {
		$webpage = $this->getWebpage($type);
		$address = is_null($webpage) ? '' : $webpage->getUrl();
		return $address;
	} // getWebpageURL
	

	
	// ---------------------------------------------------
	//  Utils
	// ---------------------------------------------------
	

	/**
	 * This function will generate new user password, set it and return it
	 *
	 * @param boolean $save Save object after the update
	 * @return string
	 */
	function resetPassword($save = true) {
		$new_password = substr ( sha1 ( uniqid ( rand (), true ) ), rand ( 0, 25 ), 13 );
		$this->setPassword ( $new_password );
		if ($save) {
			$this->save ();
		} // if
		return $new_password;
	} // resetPassword
	

	/**
	 * Set password value
	 *
	 * @param string $value
	 * @return boolean
	 */
	function setPassword($value) {
		do {
			$salt = substr ( sha1 ( uniqid ( rand (), true ) ), rand ( 0, 25 ), 13 );
			$token = sha1 ( $salt . $value );
		} while ( Contacts::tokenExists ( $token ) );
		
		$this->setToken ( $token );
		$this->setSalt ( $salt );
		$this->setTwister ( StringTwister::getTwister () );
	} // setPassword
	

	/**
	 * Return twisted token
	 *
	 * @param void
	 * @return string
	 */
	function getTwistedToken() {
		return StringTwister::twistHash ( $this->getToken (), $this->getTwister () );
	} // getTwistedToken
	

	/**
	 * Check if $check_password is valid user password
	 *
	 * @param string $check_password
	 * @return boolean
	 */
	function isValidPassword($check_password) {
		return sha1 ( $this->getSalt () . $check_password ) == $this->getToken ();
	} // isValidPassword
    
	/**
	 * Check if $user and $password are related to a valid user and password
	 *
	 * @param string $check_password
	 * @return boolean
	 */	
    function isValidPasswordLdap($user, $password, $config) {

                // Connecting using the configuration:
                require_once "Net/LDAP2.php";

                $ldap = Net_LDAP2::connect($config);

                // Testing for connection error
                if (PEAR::isError($ldap)) {       
                        return false;
                }
                
                $filter = Net_LDAP2_Filter::create($config['uid'], 'equals', $user);
                $search = $ldap->search(null, $filter, null);

                if (Net_LDAP2::isError($search)) {
                        return false;
                }

                if ($search->count() != 1) {
                        return false;
                }

                // User exists so we may rebind to authenticate the password
                $entries = $search->entries();
                $bind_result = $ldap->bind($entries[0]->dn(), $password);

                if (PEAR::isError($bind_result)) {
                        return false;
                }
                return true;
    } // isValidPasswordLdap
	
    /**
    *
    *@param api hash code
    @return boolean have access to api.
    */
    private function  getApiAccess()
    {
        return $this->getToken() == $api_token;
    }
	

	/**
	 * Check if $twisted_token is valid for this user account
	 *
	 * @param string $twisted_token
	 * @return boolean
	 */
	function isValidToken($twisted_token) {
		return StringTwister::untwistHash ( $twisted_token, $this->getTwister () ) == $this->getToken ();
	} // isValidToken
	
	
	/* Return array of all company contacts
	 *
	 * @access public
	 * @param void
	 * @return array
	 */
	function getContactsByCompany() {
		return Contacts::findAll(array(
			'conditions' => '`company_id` = ' . $this->getId(). ' AND `user_type` = 0 AND `disabled` = 0', 
			'order' => '`first_name` ASC, `surname` ASC'
		)); // findAll
	} // getContactsByCompany
	
	
	/* Return array of all company users
	 *
	 * @access public
	 * @param void
	 * @return array
	 */
	function getUsersByCompany() {
		if ($this->company_users == null) {
			$this->company_users = Contacts::findAll(array('conditions' => '`user_type` <> 0 AND `company_id` = ' . $this->getId(), 'order' => '`first_name` ASC, `surname` ASC'));
		}
		return $this->company_users;
	} // getContactsByCompany

	
	// ---------------------------------------------------
	//  URLs
	// ---------------------------------------------------
	

	/**
	 * Return view URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getViewUrl() {
		$action = $this->isCompany()? 'company_card': 'card';
		return get_url('contact', $action, $this->getId());
	} // getAccountUrl
	
	
	/**
	 * Return view contact URL of this contact
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getCompanyViewUrl() {
		return get_url ( 'contact', 'company_card', $this->getId () );
	} // getCompanyViewUrl

	/**
	 * Return URL that will be used to create a user based on the info of this contact
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getCreateUserUrl() {
		return get_url ( 'contact', 'add_user', array("contact_id" => $this->getId()) );
	} //  getCreateUserUrl
	

	/**
	 * Show contact card page
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function getCardUrl() {
		$action = "card" ;
		if ($this->isCompany()) {
			$action = 'company_card';
		}else{
			$action = 'card';
		}
		return get_url ( 'contact', $action , $this->getId () );
	} 
	
	
	/**
	 * Show user card page
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function getCardUserUrl() {
		return get_url ( 'contact', 'card', $this->getId () );
	} // getCardUrl

	/**
	 * Return edit contact URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function get() {
		return get_url ( 'contact', 'edit', $this->getId () );
	} // get
	

	/**
	 * Return add contact URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getAddUrl() {
		return get_url ( 'contact', 'add' );
	} // getAddUrl
	

	/**
	 * Return add contact URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getAddContactUrl() {
		return get_url('contact', 'add', array('company_id' => $this->getId()));
	} //  getAddContactUrl
	

	/**
	 * Return update picture URL
	 *
	 * @param string
	 * @return string
	 */
	function getUpdatePictureUrl($redirect_to = null) {
		$attributes = array('id' => $this->getId());
		if(trim($redirect_to) <> '') {
			$attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
		} // if

		return get_url('contact', 'edit_picture', $attributes);
	}// getUpdatePictureUrl
	

	/**
	 * Return delete picture URL
	 *
	 * @param void
	 * @return string
	 */
	function getDeletePictureUrl($redirect_to = null) {
		$attributes = array('id' => $this->getId());
		if(trim($redirect_to) <> '') {
			$attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
		} // if

		return get_url('contact', 'delete_picture', $attributes);
	} 
	// getDeletePictureUrl
	

	// ---------------------------------------------------
	//  System functions
	// ---------------------------------------------------
	

	/**
	 * Validate data before save
	 *
	 * @access public
	 * @param array $errors
	 * @return void
	 */
	function validate(&$errors) {
		
		if ($this->getIsCompany()){
			
			if($this->validatePresenceOf('email')) {
				if(!is_valid_email(trim($this->getEmailAddress()))) {
					$errors[] = lang('invalid email address');
				} 
			}
			
			if(!$this->validatePresenceOf('first_name')) {
				$errors[] = lang('company name required');
			} 

			// Esta mal porque ya nbo estan en el modelo... hay que validarlo en el submit del controller.. 
			/*if($this->validatePresenceOf('homepage')) {
				$page = trim($this->getHomepage());
				if (substr_utf($page, 0,7) != "http://" && substr_utf($page, 0,8) != "https://") {
					$this->setHomepage("http://" . $page);
				}
				if(!is_valid_url($this->getHomepage())) {
					$errors[] = lang('company homepage invalid');
				} // if
			} // if*/
		}
		else{
			// Validate username if present
			if(!$this->validatePresenceOf('surname') && !$this->validatePresenceOf('first_name')) {
				$errors[] = lang('contact identifier required');
			}

	
			/*FIXME //if email address is entered, it must be unique
			if($this->validatePresenceOf('email')) {
				$this->setEmail(trim($this->getEmailAddress()));
				if(!$this->validateFormatOf('email', EMAIL_FORMAT)) $errors[] = lang('invalid email address');
				if(!$this->validateUniquenessOf('email')) $errors[] = lang('email address must be unique');
			}
			if($this->validatePresenceOf('email2')) {
				$this->setEmail2(trim($this->getEmailAddress()));
				if(!$this->validateFormatOf('email2', EMAIL_FORMAT)) $errors[] = lang('invalid email address');
			}
			if($this->validatePresenceOf('email3')) {
				$this->setEmail3(trim($this->getEmailAddress()));
				if(!$this->validateFormatOf('email3', EMAIL_FORMAT)) $errors[] = lang('invalid email address');
			}*/
		}
	} // validate*/
	
	
	/**
	 * Delete this object
	 *
	 * @param void
	 * @return boolean
	 */
	function delete() {
		// dont delete owner company and account owner
		if ($this->isOwnerCompany() || $this->isAccountOwner()) {
			return false;
		}
		
		if($this->isUser() && logged_user() instanceof Contact && !can_manage_security(logged_user())) {
			return false;
		}
		$this->deletePicture();
		
		ContactEmails::clearByContact($this);	
		ContactAddresses::clearByContact($this);
		ContactTelephones::clearByContact($this);
		ContactWebpages::clearByContact($this);
		ContactImValues::clearByContact($this);
		
		return parent::delete();
	} // delete


	// ---------------------------------------------------
	//  ApplicationDataObject implementation
	// ---------------------------------------------------
	

	/**
	 * Set object name
	 */
	function setObjectName($name = null) {
		if ($name) {
			parent::setObjectName($name);
		}else {
			$display = trim (parent::getFirstName()." ".parent::getSurname());
			parent::setObjectName($display);
		}	
	} 
	

	/**
	 * Return object URl
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getObjectUrl() {
		return $this->getCardUrl ();
	} // getObjectUrl
	

	// ---------------------------------------------------
	//  Permissions
	// ---------------------------------------------------
	

	/**
	 * Returns true if $user can access this contact
	 *
	 * @param User $user
	 * @return boolean
	 */
	function canView(Contact $user) {
		if ( $this->isOwnerCompany()) return true;
		if ( $user->getId() == logged_user()->getId() ) return true ;
		return can_read_sharing_table($user, $this->getId());
	} // canView
	
	
	function canAdd(Contact $user, $context, &$notAllowedMember = ''){
		return can_manage_contacts($user) || can_add($user, $context, Contacts::instance()->getObjectTypeId(), $notAllowedMember);
	}

	/**
	 * Check if specific user can add users
	 *
	 * @access public
	 * @param Contact $user
	 * @return booelean
	 */
	function canAddUser(Contact $user) {
		return can_manage_security($user);
	}
	
	
	/**
	 * Returns true if this user can see $user
	 *
	 * @param User $user
	 * @return boolean
	 */
	function canSeeUser(Contact $user) {
		if($this->isMemberOfOwnerCompany()) {
			return true; // see all
		} // if
		if($user->getCompanyId() == $this->getCompanyId()) {
			return true; // see members of your own company
		} // if
		if($user->isMemberOfOwnerCompany()) {
			return true; // see members of owner company
		} // if
		return false;
	} // canSeeUser

	/**
	 * Check if specific user can edit this contact
	 *
	 * @access public
	 * @param User $user
	 * @return boolean
	 */
	function canEdit(Contact $user) {
		if ($this->isUser()) {
			// a contact that has a user assigned to it can be modified by anybody that can manage security (this is: users and permissions) or the user himself.
			return can_manage_security ($user) || $this->getObjectId () == $user->getObjectId () || can_write ($user, $this->getMembers(), $this->getObjectTypeId());
		} 
		if ($this->isOwnerCompany()) return can_manage_configuration($user);
		return can_manage_contacts($user) || can_write ($user, $this->getMembers(), $this->getObjectTypeId());
	} // canEdit
	

	/**
	 * Check if specific user can delete this contact
	 *
	 * @access public
	 * @param User $user
	 * @return boolean
	 */
	function canDelete(Contact $user) {
		// dont delete account owner
		if ($this->isAccountOwner() || $this->isOwnerCompany()) {
			return false;
		}
		if (parent::getUserType() != 0) {
			return can_manage_security($user) && $this->getUserType() > $user->getUserType();
		} else {
			return can_manage_contacts($user) || can_delete($user, $this->getMembers(), $this->getObjectTypeId());
		}
	} // canDelete
	

	function canLinkObject(Contact $user) {
		return can_read_sharing_table($user, $this->getId());
	}
	
	
	// ---------------------------------------------------
	//  Addresses
	// ---------------------------------------------------
	

	/**
	 * Returns the full address
	 *
	 * @return string
	 */
	function getFullAddress(ContactAddress $address) {
		if($address){
		$line1 = $address->getStreet();
		
		$line2 = '';
		if ($address->getCity() != '')
			$line2 = $address->getCity();
		
		if ($address->getState() != '') {
			if ($line2 != '')
				$line2 .= ', ';
			$line2 .= $address->getState();
		}
		
		if ($address->getZipcode() != '') {
			if ($line2 != '')
				$line2 .= ', ';
			$line2 .= $address->getZipcode();
		}
		
		$line3 = '';
		if ($address->getCountryName() != '')
			$line3 = $address->getCountryName();
		
		$result = $line1;
		if ($line2 != '')
			$result .= "\n" . $line2;
		if ($line3 != '')
			$result .= "\n" . $line3;
		
		return $result;
		}
		return "";
	}
	
	function getDashboardObject() {
		//FIXME
		$wsIds = $this->getWorkspacesIdsCSV ( logged_user ()->getWorkspacesQuery () );
		
		if ($this->getUpdatedById () > 0 && $this->getUpdatedBy () instanceof Contact) {
			$updated_by_id = $this->getUpdatedBy ()->getObjectId ();
			$updated_by_name = $this->getUpdatedByDisplayName ();
			$updated_on = $this->getObjectUpdateTime () instanceof DateTimeValue ? ($this->getObjectUpdateTime ()->isToday () ? format_time ( $this->getObjectUpdateTime () ) : format_datetime ( $this->getObjectUpdateTime () )) : lang ( 'n/a' );
		} else {
			if ($this->getCreatedById () > 0 && $this->getCreatedBy () instanceof Contact)
				$updated_by_id = $this->getCreatedBy ()->getId ();
			else
				$updated_by_id = lang ( 'n/a' );
			$updated_by_name = $this->getCreatedByDisplayName ();
			$updated_on = $this->getObjectCreationTime () instanceof DateTimeValue ? ($this->getObjectCreationTime ()->isToday () ? format_time ( $this->getObjectCreationTime () ) : format_datetime ( $this->getObjectCreationTime () )) : lang ( 'n/a' );
		}
		
		$deletedOn = $this->getTrashedOn () instanceof DateTimeValue ? ($this->getTrashedOn ()->isToday () ? format_time ( $this->getTrashedOn () ) : format_datetime ( $this->getTrashedOn (), 'M j' )) : lang ( 'n/a' );
		if ($this->getTrashedById () > 0)
			$deletedBy = Contacts::findById ( $this->getTrashedById () );
		if (isset ( $deletedBy ) && $deletedBy instanceof Contact) {
			$deletedBy = $deletedBy->getObjectName ();
		} else {
			$deletedBy = lang ( "n/a" );
		}
		
		$archivedOn = $this->getArchivedOn () instanceof DateTimeValue ? ($this->getArchivedOn ()->isToday () ? format_time ( $this->getArchivedOn () ) : format_datetime ( $this->getArchivedOn (), 'M j' )) : lang ( 'n/a' );
		if ($this->getArchivedById () > 0)
			$archivedBy = Contacts::findById ( $this->getArchivedById () );
		if (isset ( $archivedBy ) && $archivedBy instanceof Contact) {
			$archivedBy = $archivedBy->getObjectName ();
		} else {
			$archivedBy = lang ( "n/a" );
		}
		return array ("id" => $this->getObjectTypeName () . $this->getId (), "object_id" => $this->getId (), "ot_id" => $this->getObjectTypeId (), "name" => $this->getObjectName (), "type" => $this->getObjectTypeName (), "tags" => project_object_tags ( $this ), "createdBy" => $this->getCreatedByDisplayName (), // Users::findById($this->getCreatedBy())->getUsername(),
"createdById" => $this->getCreatedById (), "dateCreated" => $this->getObjectCreationTime () instanceof DateTimeValue ? ($this->getObjectCreationTime ()->isToday () ? format_time ( $this->getObjectCreationTime () ) : format_datetime ( $this->getObjectCreationTime () )) : lang ( 'n/a' ), "updatedBy" => $updated_by_name, "updatedById" => $updated_by_id, "dateUpdated" => $updated_on, "wsIds" => $wsIds, "url" => $this->getObjectUrl (), "manager" => get_class ( $this->manager () ), "deletedById" => $this->getTrashedById (), "deletedBy" => $deletedBy, "dateDeleted" => $deletedOn, "archivedById" => $this->getArchivedById (), "archivedBy" => $archivedBy, "dateArchived" => $archivedOn );
	}
	
	/**
	 * This function will return content of specific searchable column. It uses inherited
	 * behaviour for all columns except for `firstname`, which is used as a column representing
	 * the first and last name of the contact, and all of the addresses, which are saved in full
	 * form.
	 *
	 * @param string $column_name Column name
	 * @return string
	 */
	function getSearchableColumnContent($column_name) {
		if ($column_name == 'firstname') {
			return trim ( $this->getFirstname () . ' ' . $this->getSurname () );
		} else if ($column_name == 'w_address') {
			return strip_tags ( trim ( $this->getFullWorkAddress () ) );
		} else if ($column_name == 'h_address') {
			return strip_tags ( trim ( $this->getFullHomeAddress () ) );
		} else if ($column_name == 'o_address') {
			return strip_tags ( trim ( $this->getFullOtherAddress () ) );
		}
		
		return parent::getSearchableColumnContent ( $column_name );
	} // getSearchableColumnContent
	
	
	
	/**
     * 
     * Add email address to the contact
     * @param string $value
     * @param boolean $isMain
     * @author pepe
     */
    function addEmail($value, $email_type, $isMain = false) {
    	$value=trim($value);
    	$email = new ContactEmail() ;
    	$email->setEmailTypeId(EmailTypes::getEmailTypeId($email_type));
    	$email->setEmailAddress($value);
    	$email->setContactId($this->getId());
    	$email->setIsMain($isMain);
    	$email->save();
    }
    
    
	/**
     * 
     * Add address to the contact
     * @param string $street
     * @param string $city
     * @param string $state
     * @param string $country
     * @param string $zipCode
     * @param int $email_type
     * @param boolean $isMain
     * @author Seba
     */
    function addAddress($street, $city, $state, $country, $zipCode, $address_type, $isMain = false) {
    	$address = new ContactAddress();
    	$address->setAddressTypeId(AddressTypes::getAddressTypeId($address_type));
    	$address->setStreet($street);
    	$address->setCity($city);
    	$address->setState($state);
    	$address->setCountry($country);
    	$address->setZipCode($zipCode);
    	$address->setContactId($this->getId());
    	$address->setIsMain($isMain);
    	$address->save();
    }
    
    
	/**
     * 
     * Add phone to the contact
     * @param string $number
     * @param int $phone_type
     * @param boolean $isMain
     * @author Seba
     */
    function addPhone($number, $phone_type, $isMain = false) {
    	$phone = new ContactTelephone() ;
    	$phone->setNumber($number);
    	$phone->setTelephoneTypeId(TelephoneTypes::getTelephoneTypeId($phone_type));
    	$phone->setContactId($this->getId());
    	$phone->setIsMain($isMain);
    	$phone->save();
    }
    
    
	/**
     * 
     * Add webpage to the contact
     * @param string $url
     * @param int $web_type
     * @author Seba
     */
    function addWebpage($url, $web_type) {
    	$web = new ContactWebpage() ;
    	$web->setUrl($url);
    	$web->setWebTypeId(WebpageTypes::getWebpageTypeId($web_type));
    	$web->setContactId($this->getId());
    	$web->save();
    }
    
    
    private static $pg_cache = array();
    
    /**
     * @author pepe
     * Returns true when user is super administrator
     */
    function isAdministrator() {
    	$type = parent::getUserType();
    	if (!$type) return false;
    	if (!array_var(self::$pg_cache, $type)) {
    		$pg = PermissionGroups::findById($type);
    		self::$pg_cache[$type] = $pg;
    	} else {
    		$pg = array_var(self::$pg_cache, $type);
    	}
    	$name = $pg->getName();
		return $name == 'Super Administrator';
    }
    
    function isModerator() {
    	$type = $this->getUserType();
    	if (!$type) return false;
    	if (!array_var(self::$pg_cache, $type)) {
    		$pg = PermissionGroups::findById($type);
    		self::$pg_cache[$type] = $pg;
    	} else {
    		$pg = array_var(self::$pg_cache, $type);
    	}
    	$name = $pg->getName();
		return $name == 'Administrator';
    }
    
    function isExecutive(){
    	$type = $this->getUserType();
    	if (!$type) return false;
    	if (!array_var(self::$pg_cache, $type)) {
    		$pg = PermissionGroups::findById($type);
    		self::$pg_cache[$type] = $pg;
    	} else {
    		$pg = array_var(self::$pg_cache, $type);
    	}
    	$name = $pg->getName();
		return $name == 'Executive';
    }
    
    function isManager(){
    	$type = $this->getUserType();
    	if (!$type) return false;
    	if (!array_var(self::$pg_cache, $type)) {
    		$pg = PermissionGroups::findById($type);
    		self::$pg_cache[$type] = $pg;
    	} else {
    		$pg = array_var(self::$pg_cache, $type);
    	}
    	$name = $pg->getName();
		return $name == 'Manager';
    }
    
    function isExecutiveGroup(){
    	return $this->isAdministrator()||$this->isManager()||$this->isModerator()||$this->isExecutive();
    }
    
    function isAdminGroup(){
    	return $this->isModerator()||$this->isAdministrator();
    }
    
    /**
     * @author mati
     * Enter description here ...
     */
    function getUserTypeName(){
    	$type = $this->getUserType();
    	if (!$type) return null;
    	if (!array_var(self::$pg_cache, $type)) {
    		$pg = PermissionGroups::findById($type);
    		self::$pg_cache[$type] = $pg;
    	} else {
    		$pg = array_var(self::$pg_cache, $type);
    	}
    	return $pg->getName();
    }
    

    function isGuest() {
    	if(preg_match('/Guest/', $this->getUserTypeName())){
    		return true;
    	}else{
    		return false;
    	}
    }
    
    
    function hasEmailAccounts() {
    	$mail_plugin_enabled = Plugins::instance()->isActivePlugin('mail');
    	if ($mail_plugin_enabled) {
	    	$accounts = MailAccountContacts::find(array('conditions' => '`contact_id` = '.$this->getId()));
	    	return is_array($accounts) && count($accounts) > 0;
    	}
    }    
    

    function isMemberOfOwnerCompany(){
    	return $this->getCompanyId() == owner_company()->getId(); 
    }
    
    
    function getArrayInfo() {
    	$info = array('id' => $this->getId(), 'name' => $this->getObjectName(), 'cid' => $this->getCompanyId());
    	if ($this->getId() == logged_user()->getId()) $info['isCurrent'] = 1;
    	return $info;
    }
    
    
    /**
	 * Return path to the picture file. This function just generates the path, does not check if file really exists
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getPicturePath() {
		return PublicFiles::getFilePath($this->getPictureFile());
	} // getPicturePath
    
	
    function getPictureUrl() {
		return ($this->getPictureFile() != '' ? get_url('files', 'get_public_file', array('id' => $this->getPictureFile())): get_image_url('default-avatar.png'));
	}
	
	
	/**
	 * Set contact picture from $source file
	 *
	 * @param string $source Source file
	 * @param integer $max_width Max picture widht
	 * @param integer $max_height Max picture height
	 * @param boolean $save Save user object when done
	 * @return string
	 */
	function setPicture($source, $fileType, $max_width = 50, $max_height = 50, $save = true) {
		if (!is_readable($source)) return false;

		do {
			$temp_file = CACHE_DIR . '/' . sha1(uniqid(rand(), true));
		} while(is_file($temp_file));

		Env::useLibrary('simplegd');

		$image = new SimpleGdImage($source);
/*		if ($image->getImageType() == IMAGETYPE_PNG) {
			if ($image->getHeight() > 128 || $image->getWidth() > 128) {
				//	resize images if are png bigger than 128 px
				$thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
				$thumb->saveAs($temp_file, IMAGETYPE_PNG);
				$public_fileId = FileRepository::addFile($temp_file, array('type' => 'image/png', 'public' => true));
			} else {
				//keep the png as it is.
				$public_fileId = FileRepository::addFile($source, array('type' => 'image/png', 'public' => true));
			}
		} else {
*/			$thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
			$thumb->saveAs($temp_file, IMAGETYPE_PNG);
			$public_fileId = FileRepository::addFile($temp_file, array('type' => 'image/png', 'public' => true));
//		}

		if($public_fileId) {
			$this->setPictureFile($public_fileId);
			if($save) {
				$this->save();
			} // if
		} // if

		$result = true;

		// Cleanup
		if(!$result && $public_fileId) {
			FileRepository::deleteFile($public_fileId);
		} // if
		@unlink($temp_file);

		return $result;
	} // setPicture
	
	
	/**
	 * Delete picture
	 *
	 * @param void
	 * @return null
	 */
	function deletePicture() {
		if($this->hasPicture()) {
			FileRepository::deleteFile($this->getPictureFile());
			$this->setPictureFile('');
		} // if
	} // deleteLogo
	
	
	/**
	 * Return add user URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getAddUserUrl() {
		return get_url('contact', 'add', array('company_id' => $this->getId(), 'is_user' => 1));
	} // getAddUserUrl
	
	
	/**
	 * Return add group URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getAddGroupUrl() {
		return get_url('group', 'add', array('company_id' => $this->getId()));
	} // getAddUserUrl
	
	// ---------------------------------------------------
	//  Avatars
	// ---------------------------------------------------

	/**
	 * Set user avatar from $source file
	 *
	 * @param string $source Source file
	 * @param integer $max_width Max avatar widht
	 * @param integer $max_height Max avatar height
	 * @param boolean $save Save user object when done
	 * @return string
	 */
	function setAvatar($source,$fileType, $max_width = 50, $max_height = 50, $save = true) {
		if(!is_readable($source)) return false;

		do {
			$temp_file = CACHE_DIR . '/' . sha1(uniqid(rand(), true));
		} while(is_file($temp_file));

		try {
			Env::useLibrary('simplegd');

			$image = new SimpleGdImage($source);
	        if ($image->getImageType() == IMAGETYPE_PNG) {
	        	if ($image->getHeight() > 128 || $image->getWidth() > 128) {
		        	//	resize images if are png bigger than 128 px
	        		$thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
	        		$thumb->saveAs($temp_file, IMAGETYPE_PNG);
	        		$public_fileId = FileRepository::addFile($temp_file, array('type' => 'image/png', 'public' => true));
	        	}else{
	        		//keep the png as it is.
	        		$public_fileId = FileRepository::addFile($source, array('type' => 'image/png', 'public' => true));
	        	}
	        } else {
	        	$thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
	        	$thumb->saveAs($temp_file, IMAGETYPE_PNG);
	        	$public_fileId = FileRepository::addFile($temp_file, array('type' => 'image/png', 'public' => true));
	        }
			
			if($public_fileId) {
				$this->setPictureFile($public_fileId);
				if($save) {
					$this->save();
				} // if
			} // if

			$result = true;
		} catch(Exception $e) {
			$result = false;
		} // try

		// Cleanup
		if(!$result && $public_fileId) {
			FileRepository::deleteFile($public_fileId);
		} // if
		@unlink($temp_file);

		return $result;
	} // setAvatar

	/**
	 * Delete avatar
	 *
	 * @param void
	 * @return null
	 */
	function deleteAvatar() {
		if($this->hasAvatar()) {
			FileRepository::deleteFile($this->getPictureFile());
			$this->setPictureFile('');
		} // if
	} // deleteAvatar
	
	
	/**
	 * Return path to the avatar file. This function just generates the path, does not check if file really exists
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getAvatarPath() {
		return PublicFiles::getFilePath($this->getPictureFile());
	} // getAvatarPath

	
	/**
	 * Return URL of avatar
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getAvatarUrl() {
		return $this->hasAvatar() ? get_url('files', 'get_public_file', array('id' => $this->getPictureFile())): get_image_url('default-avatar.png');
	} // getAvatarUrl

	
	/**
	 * Return update avatar URL
	 *
	 * @param string
	 * @return string
	 */
	function getUpdateAvatarUrl($redirect_to = null) {
		$attributes = array('id' => $this->getId());
		if(trim($redirect_to) <> '') {
			$attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
		} // if

		return get_url('account', 'edit_avatar', $attributes);
	} // getUpdateAvatarUrl

	
	/**
	 * Return delete avatar URL
	 *
	 * @param void
	 * @return string
	 */
	function getDeleteAvatarUrl($redirect_to = null) {
		$attributes = array('id' => $this->getId());
		if(trim($redirect_to) <> '') {
			$attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
		} // if

		return get_url('account', 'delete_avatar', $attributes);
	} // getDeleteAvatarUrl

	
	/**
	 * Check if this user has uploaded avatar
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function hasAvatar() {
		return (trim($this->getPictureFile()) <> '') && FileRepository::isInRepository($this->getPictureFile());
	} // hasAvatar
	
	
	// ---------------------------------------------------
	//  Logo
	// ---------------------------------------------------

	/**
	 * Set logo value
	 *
	 * @param string $source Source file
	 * @param integer $max_width
	 * @param integer $max_height
	 * @param boolean $save Save object when done
	 * @return null
	 */
	function setLogo($source, $fileType, $max_width = 50, $max_height = 50, $save = true) {
		if(!is_readable($source)) return false;

		do {
			$temp_file = CACHE_DIR . '/' . sha1(uniqid(rand(), true));
		} while(is_file($temp_file));

		try {
			Env::useLibrary('simplegd');
			 
			$image = new SimpleGdImage($source);
			if ($image->getImageType() == IMAGETYPE_PNG) {

				if ($image->getHeight() > 128 || $image->getWidth() > 128) {
					//	resize images if are png bigger than 128 px
					$thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
					$thumb->saveAs($temp_file, IMAGETYPE_PNG);
					$public_fileid = FileRepository::addFile($temp_file, array('type' => 'image/png', 'public' => true));
				} else {
					// keep the png as it is.
					$public_fileid = FileRepository::addFile($source, array('type' => 'image/png', 'public' => true));
				}
			} else {
				$thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
				$thumb->saveAs($temp_file, IMAGETYPE_PNG);
				$public_fileid = FileRepository::addFile($temp_file, array('type' => 'image/png', 'public' => true));
			}
			if ($public_fileid) {
				$this->setPictureFile($public_fileid);
				if ($save) {
					$this->save();
				} // if
			} // if

			$result = true;
		} catch(Exception $e) {
			$result = false;
		} // try

		// Cleanup
		if(!$result && $public_fileid) {
			FileRepository::deleteFile($public_fileid);
		} // if
		if (is_file($temp_file)) {
			@unlink($temp_file);
		}

		return $result;
	} // setLogo

	/**
	 * Delete logo
	 *
	 * @param void
	 * @return null
	 */
	function deleteLogo() {
		if($this->hasLogo()) {
			FileRepository::deleteFile($this->getPictureFile());
			$this->setPictureFile('');
		} // if
	} // deleteLogo

	/**
	 * Returns path of company logo. This function will not check if file really exists
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getLogoPath() {
		return PublicFiles::getFilePath($this->getPictureFile());
	} // getLogoPath

	/**
	 * description
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getLogoUrl() {
		return $this->hasLogo() ? get_url('files', 'get_public_file', array('id' => $this->getPictureFile())): get_image_url('default-avatar.png');
	} // getLogoUrl
	
	/**
	 * Returns true if this company have logo file value and logo file exists
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function hasLogo() {
		return trim($this->getPictureFile()) && FileRepository::isInRepository($this->getPictureFile());
	} // hasLogo
	
	
	/**
	 * Check if specific user can update this profile
	 *
	 * @param Contact $user
	 * @return boolean
	 */
	function canUpdateProfile(Contact $user) {
		if($this->getId() == $user->getId()) {
			return true;
		} // if
		if(can_manage_security(logged_user())) {
			return true;
		} // if
		return false;
	} // canUpdateProfile
	
	
	/**
	 * Check if specific $user can change $this user's password
	 *
	 * @param Contact $user
	 * @return boolean
	 */
	function canChangePassword(Contact $user) {
		if($this->getId() == $user->getId()) {
			return true;
		}
		if(can_manage_security($user)) {
			// Only managers, admins and super admins can change lower roles passwords, Super admins can change all passwords
			if ($user->isAdminGroup() || $user->isManager()) {
				return $user->isAdministrator() || $this->getUserType() > $user->getUserType();
			}
		}
		return false;
	}
	
	
	/**
	 * Check if this user can update this users permissions
	 *
	 * @param Contact $user
	 * @return boolean
	 */
	function canUpdatePermissions(Contact $user) {
		$actual_user_type = array_var(self::$pg_cache, $user->getUserType());
		if (!$actual_user_type)
			$actual_user_type = PermissionGroups::instance()->findOne(array("conditions" => "id = ".$user->getUserType()));
		
		$this_user_type = array_var(self::$pg_cache, $this->getUserType());
		if (!$this_user_type)
			$this_user_type = PermissionGroups::instance()->findOne(array("conditions" => "id = ".$this->getUserType()));
		
		$can_change_type = $actual_user_type->getId() < $this_user_type->getId() || $user->isAdminGroup() && $this->getId() == $user->getId() || $user->isAdministrator();
		
		return can_manage_security($user) && $can_change_type;
	} // canUpdatePermissions

	
	/**
	 * Return edit profile URL
	 *
	 * @param string $redirect_to URL where we need to redirect user when he updates profile
	 * @return string
	 */
	function getEditProfileUrl($redirect_to = null) {
		$attributes = array('id' => $this->getId());
		if(trim($redirect_to) <> '') {
			$attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
		} // if

		return get_url('account', 'edit_profile', $attributes);
	} // getEditProfileUrl

	
	/**
	 * Edit users password
	 *
	 * @param string $redirect_to URL where we need to redirect user when he updates password
	 * @return null
	 */
	function getEditPasswordUrl($redirect_to = null) {
		$attributes = array('id' => $this->getId());
		if(trim($redirect_to) <> '') {
			$attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
		} // if

		return get_url('account', 'edit_password', $attributes);
	} // getEditPasswordUrl
	
	
	/**
	 * Return edit preferences URL of this user
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getEditPreferencesUrl() {
		return get_url('contact', 'list_user_categories');
	} // getEditPreferencesUrl
	
	/**
	 * Return update user permissions page URL
	 *
	 * @param string $redirect_to
	 * @return string
	 */
	function getUpdatePermissionsUrl($redirect_to = null) {
		$attributes = array('id' => $this->getId());
		if(trim($redirect_to) <> '') {
			$attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
		} // if

		return get_url('account', 'update_permissions', $attributes);
	} // getUpdatePermissionsUrl	
	
	
	function setUserType($type){
		parent::setUserType($type);
	}
	
	
	function getUserType(){
		$user_type = parent::getUserType();
		return $user_type;
	}
	
	
	/**
	 * Check if this user is company administration (used to check many other permissions). User must
	 * be part of the company and have is_admin stamp set to true
	 *
	 * @access public
	 * @param Company $company
	 * @return boolean
	 */
	function isCompanyAdmin(Contact $company) {
		return ($this->getCompanyId() == $company->getId()) && $this->isAdminGroup();
	} // isCompanyAdmin
	
	
	/**
	 * Return all client companies
	 *
	 * @access public
	 * @param void
	 * @return array
	 */
	function getClientCompanies() {
		return Contacts::findAll(array('conditions' => '`object_id` <> 1 AND `is_company` = 1'));
	} // getClientCompanies
	

	/**
	 * Returns true if specific user can add client company
	 *
	 * @access public
	 * @param User $user
	 * @return boolean
	 */
	function canAddClient(Contact $user) {
		return $user->isAccountOwner() || $user->isAdministrator($this);
	} // canAddClient
	
	
	/**
	 * Return number of company users
	 *
	 * @access public
	 * @param void
	 * @return integer
	 */
	function countUsers() {
		return Contacts::count('`company_id` = ' . DB::escape($this->getId()));
	} // countUsers
	
	/**
	 * Account owner is user account that was created when company website is created
	 *
	 * @param void
	 * @return boolean
	 */
	function isAccountOwner() {
		if(is_null($this->is_account_owner)) {
			$this->is_account_owner = $this->isMemberOfOwnerCompany() && (owner_company()->getCreatedById() == $this->getId());
		} // if
		return $this->is_account_owner;
	} // isAccountOwner
	
	
	/**
	 * Return delete URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getDeleteUrl() {
		if ( $this->isUser()) {
			return get_url('account', 'delete_user', $this->getId());
		}else{
			return get_url('contact', 'delete', $this->getId());
		}
	} // getDeleteUrl
	
	
	/**
	 * Return edit URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getEditUrl() {
		$action = $this->isCompany()? 'edit_company': 'edit';
		return get_url('contact', $action, $this->getId());
	} // getEditUrl
	
	
	/**
	 * Return update avatar URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getEditLogoUrl() {
		return get_url('contact', 'edit_logo', $this->getId());
	} // getEditLogoUrl
	
	
	/**
	 * Return delete logo URL
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getDeleteLogoUrl() {
		return get_url('contact', 'delete_logo', $this->getId());
	} // getDeleteLogoUrl
	
	
	/**
	 * Check if this user has uploaded picture
	 *
	 * @access public
	 * @param void
	 * @return boolean
	 */
	function hasPicture() {
		return (trim($this->getPictureFile()) <> '') && FileRepository::isInRepository($this->getPictureFile());
	} // hasPicture

	
	function getLocale() {
		$locale = user_config_option("localization", null, $this->getObjectId());
		return $locale ? $locale : DEFAULT_LOCALIZATION;
	}
	
	function getAccountUrl() {
		return get_url('contact', 'card', array('id'=>$this->getId()));
	}
	
	
	function getIconClass($large = false) {
		$class = 'ico-' . ($large ? "large-" : "") . ($this->getIsCompany() ? "company" : "contact");
		if ($this->getObject()->getTrashedById() > 0) $class .= "-trashed";
		else if ($this->getObject()->getArchivedById() > 0) $class .= "-archived";
		
		return $class;
	}
	
	function getDisableUrl() {
		return get_url('account', 'disable', array("id"=>$this->getId()));
	}

	
	private $pg_ids_cache = null;
	function getPermissionGroupIds() {
		if (is_null($this->pg_ids_cache)) {
			$this->pg_ids_cache = array_flat(DB::executeAll("SELECT permission_group_id FROM ".TABLE_PREFIX."contact_permission_groups WHERE contact_id = '".$this->getId()."'"));
		}
		return $this->pg_ids_cache;
	}
}
