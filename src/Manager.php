<?php
namespace Nonagod\UserActions;

use \Nonagod\Exceptions\SystemException;
use \Nonagod\Exceptions\ProgrammingException;
use \Nonagod\Exceptions\UserException;


class Manager {
    private const ACTION_VARIABLE_NAME = 'user_action';

    private const BUFFER_ACTION_NAME = 'buffer';
    private const CONTENT_PART_VARIABLE_NAME = 'part';

    private ?string $absolute_path_to_handlers_folder = null;

    private ?string $user_action = null;
    private ?string $name_of_content_part = null;

    private bool $is_buffer_action = false;
    private bool $is_content_part_start_defined = false;
    private bool $is_response_sent = false;


    public function __construct( $absolute_path_to_handlers_folder ) {
        $this->absolute_path_to_handlers_folder = $absolute_path_to_handlers_folder;
        $this->checkRequestForAnAction();

        try {
            if( $this->user_action ) {
                if( !$this->is_buffer_action ) {
                    $this->handleAction( $this->user_action );
                    throw new ProgrammingException('Wrong exit from action handler. Answer doesn\'t set.');
                }else {
                    if( !ob_start() ) throw new SystemException('Buffering cannot be enabled!');
                }
            }
        }catch( UserException $Exception ) {
            $this->failed( $Exception->getSymbolicCode( ), $Exception->getMessage( ), $Exception->getAdditionalInfo( ));
        }
    }
    public function __destruct( ) {
        if( !$this->is_response_sent && $this->name_of_content_part && $this->is_buffer_action ) {
            ob_end_clean();
            throw new ProgrammingException( 'Buffer: Borders of content part is undefined!' );
        }
    }


    protected function succeed( $answer_data = null ) {
        $this->sendResponse( true, $answer_data );
    }
    //abort
    protected function failed( string $code, string $msg = null, $error_info = null ) {
        $this->sendResponse( false, array(
            'code' => $code,
            'msg' => $msg,
            'info' => $error_info
        ));
    }
    protected function sendResponse( bool $status = null, $result = null ) {
        print_r(
            json_encode(
                array(
                    'status' => $status,
                    'result' => $result
                )
            )
        );
        $this->is_response_sent = true;
        die();
    }

    protected function handleAction( string $action_name ) {
        $path_to_handler = $this->absolute_path_to_handlers_folder . DIRECTORY_SEPARATOR . $action_name . '.php';

        if( !file_exists($path_to_handler)) throw new ProgrammingException( 'Action handler is undefined!' );

        require $path_to_handler;
    }
    protected function checkRequestForAnAction() {
        $this->user_action = filter_input(INPUT_POST, Manager::ACTION_VARIABLE_NAME, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        if( $this->is_buffer_action = boolval($this->user_action === Manager::BUFFER_ACTION_NAME )) {
            if( !$this->name_of_content_part = filter_input(INPUT_POST, Manager::CONTENT_PART_VARIABLE_NAME, FILTER_SANITIZE_FULL_SPECIAL_CHARS )) {
                throw new ProgrammingException( 'Buffer: Name of content part is missing!' );
            }
        }
    }


    public function defineStartOfContentPart( $part_name ) {
        if( $this->is_buffer_action && $this->name_of_content_part === $part_name) {
            $this->is_content_part_start_defined = true;

            for( $i = 0; $i < ob_get_level()+1; $i++ ) {
                $trashed_content = ob_get_clean();
                unset($trashed_content);
            }

            ob_start();
        }
    }
    public function defineEndOfContentPart( $part_name ) {
        if( $this->is_buffer_action && $this->name_of_content_part === $part_name ) {
            $content = ob_get_contents();
            ob_clean();

            if( !$this->is_content_part_start_defined ) throw new ProgrammingException( 'Buffer: Content part start is not defined!' );

            $this->succeed( $content );
        }
    }
}