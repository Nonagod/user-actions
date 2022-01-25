<?php
/**
 * @var Nonagod\UserActions\UserActionsManager $this
 */

$hello_to = filter_input(INPUT_POST, 'hello_to', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$this->setResponse(true, "Hello $hello_to!");