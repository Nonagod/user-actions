<?php
/**
 * @var Nonagod\UserActions\UserActionsManager $this
 */

$say_hello_result = $this->handleAction('sayHello');
$say_something_result = $this->handleAction('_system/saySomething');

if( $say_hello_result['status'] && $say_something_result['status'] ) {
    $this->setResponse(true, $say_hello_result['result'] .' '. $say_something_result['result']);
}

