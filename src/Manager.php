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


    public function __construct( $relative_path_to_handlers_folder = '/_resources/UAM' ) {
        $this->absolute_path_to_handlers_folder = $_SERVER['DOCUMENT_ROOT'] . $relative_path_to_handlers_folder;
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


    protected function succeed( $answer_data ) {
        $this->sendResponse( true, $answer_data );
    }
    //abort
    protected function failed( string $code, string $msg = null, $error_info = null) {
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

// Правила
// Каждый обработчик ДОЛЖЕН устанавливать ответ (успешный и неуспешный)!
// Капча и логирование запросов делается в конкретном обработчике (в частности). Можно выносить в общий файл.

// Текстовое сообщение нужно не во всех случаях (в основном только при ошибках).

// На подумать
// todo подумать нужно ли делать проверку на вызов в обработчике setResponse (HANDLER_WRONG_EXIT)
// todo подумать нужно ли делать сбрасывать _tmp_response после завершения, иначе комплекс без явного указания возвращает последний
// todo сделать отлов других ошибок в конструкторе и их логирование, а пользователю общий ответ (что-то не так)
// Можно сделать для обработчиков свой класс, чтобы убрать рутину из обработчиков. (+ управлять валидацией и откатом)