<?php
namespace Nonagod\UserActions;

use \Nonagod\Service\Exceptions\UserException;
use \Nonagod\Service\Exceptions\SystemException;



class UserActionsManager {
    use \Nonagod\Service\LoggerTrait;

    private const ACTION_VARIABLE_NAME = 'user_action';
    private const BUFFER_ACTION_NAME = 'buffer';
    private const CONTENT_PART_VARIABLE_NAME = 'part';

    private ?string $absolute_path_to_handlers_folder = null;

    private ?string $user_action =  null;
    private ?string $content_part_name =  null;

    private array $_tmp_response = array(
        'status' => false,
        'result' => null
    );

    private bool $is_response_sent = false;
    private bool $is_buffer_action = false;
    private bool $is_combine_buffer = false;
    private bool $is_content_part_start_defined = false;

    public function __construct( $relative_path_to_handlers_folder = '/_resources/UAM' ) {
        $this->absolute_path_to_handlers_folder = $_SERVER['DOCUMENT_ROOT'] . $relative_path_to_handlers_folder;
        $this->checkRequestForAnAction();

        try {
            if( $this->user_action ) {
                if( !$this->is_buffer_action ) $this->handleAction( $this->user_action );

                if( !$this->is_buffer_action ) {
                    $this->sendResponse();
                }else {
                    if( !ob_start() ) {$this->sendResponse();} //todo log ContentBufferingCannotBeEnableException
                }
            }

        }catch( UserException $e ) {
            $this->sendResponse(false, $e->getMessage() );
        }catch( SystemException $e ) {
            // todo log Exceptions
        }

    }
    public function __destruct() {
        if( !$this->is_response_sent && $this->content_part_name && $this->is_buffer_action ) {
            // finalizeBuffer
            ob_end_clean(); //ob_end_flush();
            //todo log CONTENT_PART_BORDERS_UNDEFINED
        }
    }

    protected function checkRequestForAnAction() {
        $this->user_action = filter_input(INPUT_POST, UserActionsManager::ACTION_VARIABLE_NAME, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if( $this->is_buffer_action = boolval($this->user_action === UserActionsManager::BUFFER_ACTION_NAME) ) {
            if(!$this->content_part_name = filter_input(INPUT_POST, UserActionsManager::CONTENT_PART_VARIABLE_NAME, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {}  //todo log ContentPartNameMissingException
        }
    }

    protected function handleAction( string $action_name ) {
        $path_to_handler = $this->absolute_path_to_handlers_folder . DIRECTORY_SEPARATOR . $action_name . '.php';

        if( file_exists($path_to_handler) ) require $path_to_handler;
        //else todo log HandlerUndefinedException

        return $this->_tmp_response;
    }

    protected function setResponse( bool $status = false, $result = null ) {
        $this->_tmp_response['status'] = $status;
        $this->_tmp_response['result'] = $result;
    }
    protected function sendResponse( bool $status = null, $result = null ) {
        print_r(
            json_encode(
                array(
                    'status' => $status ?? $this->_tmp_response['status'],
                    'result' => $result ?? $this->_tmp_response['result']
                )
            )
        );
        $this->is_response_sent = true;
        die();
    }


    protected function setBuffer( string $content_part_name ) {
        $this->is_buffer_action = true;
        $this->is_combine_buffer = true;
        $this->content_part_name = $content_part_name;
    }
    public function defineStartOfContentPart( $part_name ) {
        if( $this->is_buffer_action && $this->content_part_name === $part_name) {
            $this->is_content_part_start_defined = true;

            for( $i = 0; $i < ob_get_level()+1; $i++ ) {
                $trashed_content = ob_get_clean();
                unset($trashed_content);
            }

            ob_start();
        }
    }
    public function defineEndOfContentPart( $part_name ) {
        if( $this->is_buffer_action && $this->content_part_name === $part_name ) {
            $content = ob_get_contents();
            ob_clean();

            if( !$this->is_content_part_start_defined ) {
                //todo log DEFINITION_START_OF_CONTENT_PART_MISSING
                $this->sendResponse();
            }

            if( $this->is_combine_buffer && !$this->_tmp_response['status'] ) {
                // todo log COMPLEX_HANDLER_ERROR
                $this->sendResponse();
            }

            $this->sendResponse(true, $this->is_combine_buffer ? array($content, $this->_tmp_response['result']) : $content);
        }
    }
}

// Правила
// Каждый обработчик ДОЛЖЕН устанавливать ответ (успешный и неуспешный)!
// Капча и логирование запросов делается в конкретном обработчике (в частности).

// На подумать
// todo подумать нужно ли делать проверку на вызов в обработчике setResponse (HANDLER_WRONG_EXIT)
// todo подумать нужно ли делать сбрасывать _tmp_response после завершения, иначе комплекс без явного указания возвращает последний
// Можно сделать для обработчиков свой класс, чтобы убрать рутину из обработчиков. (+ управлять валидацией и откатом)