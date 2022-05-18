<?php
/**
 * @var Nonagod\UserActions\Manager $this
 */

$hello_to = filter_input(INPUT_POST, 'hello_to', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$this->succeed("Hello $hello_to!");