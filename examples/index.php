<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \Nonagod\UserActions\UserActionsManager;

$AM = new UserActionsManager('/examples/_resources/UAM');

?>

<style>
    .wrapper {
        width: 500px;
        margin: 0 auto;
        margin-bottom: 25px;
    }
    .wrapper-buttons {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .wrapper-buttons div {
        padding: 5px;
        border: 1px solid black;
        cursor: pointer;
        margin-bottom: 5px;
    }
    .wrapper-buttons div:hover {
        background: #000;
        color: #fff;
    }
    form {
        display: flex;
        flex-direction: column;
    }
    form * {
        margin-bottom: 10px;
    }
</style>


<div class="wrapper wrapper-buttons">
    <?$AM->defineStartOfContentPart('cases');?>
    <div class="js-setFormData" data-preset_data_number="1">Привет мир</div>
    <div class="js-setFormData" data-preset_data_number="2">Запросить контент</div>
    <div class="js-setFormData" data-preset_data_number="3">Комплексное (буфер)</div>
    <div class="js-setFormData" data-preset_data_number="4">Комплексное (говори)</div>
    <?$AM->defineEndOfContentPart('cases');?>
</div>

<div class="wrapper">
    <form>
        <input type="text" name="user_action" />
        <textarea name="json" id="" cols="30" rows="10"></textarea>
        <button type="submit">Send</button>
    </form>
</div>
<div class="wrapper js-response_container"></div>


<script>
    /*common*/
    /*NG functions + NGRequest*/
    "use strict";function _instanceof(e,t){return null!=t&&"undefined"!=typeof Symbol&&t[Symbol.hasInstance]?!!t[Symbol.hasInstance](e):e instanceof t}function _classCallCheck(e,t){if(!_instanceof(e,t))throw new TypeError("Cannot call a class as a function")}function _defineProperties(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function _createClass(e,t,r){return t&&_defineProperties(e.prototype,t),r&&_defineProperties(e,r),e}function _defineProperty(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function _slicedToArray(e,t){return _arrayWithHoles(e)||_iterableToArrayLimit(e,t)||_unsupportedIterableToArray(e,t)||_nonIterableRest()}function _nonIterableRest(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function _unsupportedIterableToArray(e,t){if(e){if("string"==typeof e)return _arrayLikeToArray(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?_arrayLikeToArray(e,t):void 0}}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}function _iterableToArrayLimit(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var r=[],n=!0,o=!1,a=void 0;try{for(var i,s=e[Symbol.iterator]();!(n=(i=s.next()).done)&&(r.push(i.value),!t||r.length!==t);n=!0);}catch(e){o=!0,a=e}finally{try{n||null==s.return||s.return()}finally{if(o)throw a}}return r}}function _arrayWithHoles(e){if(Array.isArray(e))return e}!function(e){var t;e.NG={get_params:(t={},e.location.search.substr(1).split("&").forEach(function(e,r){var n=_slicedToArray(e.split("="),2),o=n[0],a=n[1];a=a&&decodeURIComponent(a),t[o]?Array.isArray(t[o])?t[o].push(a):t[o]=[t[o],a]:t[o]=a}),t),getParam:function(t){return e.NG.get_params[t]},formatObjectToFormDataObject:function(e){var t=new FormData;if("object"===_typeof(e))for(var r=0,n=Object.entries(e);r<n.length;r++){var o=_slicedToArray(n[r],2),a=o[0],i=o[1];t.append(a,i)}return t}}}(window),function(e){var t=function(){function e(){_classCallCheck(this,e),_defineProperty(this,"_debug",void 0),_defineProperty(this,"_last_data",void 0),_defineProperty(this,"FormData",void 0),_defineProperty(this,"user_handlers",void 0),_defineProperty(this,"url",void 0);var t=window.NG.getParam("debug");t&&(this._debug=Array.isArray(t)?Boolean(-1!==t.indexOf("NGRequest")):Boolean("NGRequest"===t))}return _createClass(e,[{key:"setData",value:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:void 0,r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:void 0;this._debug&&console.log("NGRequest.setData",e,t,r),this.FormData=this._validateData(e),this.user_handlers=this._validateHandlers(t),this.url=this._validateUrl(r)}},{key:"_validateData",value:function(e){if("object"===_typeof(e))return"[object FormData]"===Object.prototype.toString.call(e)?e:window.NG.formatObjectToFormDataObject(e)}},{key:"_validateUrl",value:function(e){return"string"==typeof e&&""!==e||(e=window.location.href),e}},{key:"_validateHandlers",value:function(e){var t=function(e){console.log(e)},r=function(e){console.warn(e)},n=_typeof(e);return e&&("function"===n&&(t=e),"object"===n&&(e.hasOwnProperty("success")&&(t=e.success),e.hasOwnProperty("error")&&(r=e.error))),{success:t,error:r}}},{key:"send",value:function(){this._checkData()&&fetch(this.url,{method:"POST",body:this.FormData}).then(this._responseHandler.bind(this)).then(this._resultHandler.bind(this)).catch(this._systemErrorHandler.bind(this))}},{key:"_checkData",value:function(){return this._last_data!==this.FormData&&(this._last_data=this.FormData,!0)}},{key:"_responseHandler",value:function(e){if(this._debug&&console.log(e),!e.ok)throw new Error("".concat(e.status," ").concat(e.statusText));return e.json()}},{key:"_resultHandler",value:function(e){if(this._debug&&console.log(e),!this.user_handlers.hasOwnProperty("success")||!this.user_handlers.hasOwnProperty("error"))throw new Error("Handlers not defined.");e.status?this.user_handlers.success(e.result):this.user_handlers.error(e.result)}},{key:"_systemErrorHandler",value:function(e){console.error(e)}}]),e}();e.NGRequest=new t}(window);


    function addEventListenerByClassName( cl, event_name, listener_function, parent_element = document ) {
        let dom_elements = parent_element.getElementsByClassName( cl );
        if( dom_elements ) {
            for( let [index, element] of Object.entries(dom_elements) ) {
                element.addEventListener( event_name, listener_function );
            }
        }
    }
    /*common*/

    /*project code*/
    let preset_data = {
        1: {
            'action': 'sayHello',
            'json': {
                'hello_to': 'мир'
            }
        },
        2: {
            'action': 'buffer',
            'json': {
                'part': 'cases'
            }
        },
        3: {
            'action': 'complex/with_buffer',
            'json': {
                'hello_to': 'мир',
                'part': 'cases'
            }
        },
        4: {
            'action': 'complex/speak',
            'json': {
                'hello_to': 'мир',
            }
        }
    };

   window.addEventListener('load', () => {
       let form_elements = {
           form: document.querySelector('form'),
           action_input: document.querySelector('[name="user_action"]'),
           json_textarea: document.querySelector('[name="json"]')
       };

       // cases buttons
       addEventListenerByClassName('js-setFormData', 'click', (e) => {
           let preset_data_number = e.currentTarget.dataset.preset_data_number;

           if( preset_data.hasOwnProperty(preset_data_number) ) {
               form_elements.action_input.value = preset_data[preset_data_number].action;
               form_elements.json_textarea.value = JSON.stringify(preset_data[preset_data_number].json);
           }else alert('Нет такого кейса.');
       });

       //form submit
       form_elements.form.addEventListener('submit', (e) => {
           e.preventDefault();
           let json = null;
           try {
               json = JSON.parse(form_elements.json_textarea.value);
               json['user_action'] = form_elements.action_input.value;

               NGRequest.setData(json, {success: (d) => {
                   document.querySelector('.js-response_container').innerHTML = d;
               }});
               NGRequest.send();

               console.log( json );
           }catch (e) {
               alert('JSON have an error.');
           }
       });
   });
    /*project code*/

</script>

<?//$AM->finalizeBuffer();?>