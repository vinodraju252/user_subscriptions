<?php
// $emailValues = array(
// 	'username' => 'My username value here',
// 	'password' => 'My password value here',
// );

// $emailHtml = new EmailParser($emailValues);
// echo $emailHtml->output();

class EmailParser {
	protected $_openingTag = '{{';
	protected $_closingTag = '}}';
	protected $_emailValues;

	public function parse($emailValues, $template) {
		$this->_emailValues = $emailValues;
		$html = file_get_contents($template . '.html');
		foreach ($this->_emailValues as $key => $value) {
			$html = str_replace($this->_openingTag . $key . $this->_closingTag, $value, $html);
		}
		return $html;
	}
}