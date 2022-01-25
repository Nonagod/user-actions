<?php
/**
 * @var Nonagod\UserActions\UserActionsManager $this
 */

$say_something_result = $this->handleAction('_system/saySomething');

$this->setResponse(true, $say_something_result['result']);
$this->setBuffer(filter_input(INPUT_POST, 'part', FILTER_SANITIZE_FULL_SPECIAL_CHARS));