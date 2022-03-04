<?php

namespace App\Helpers;

class ErrorHelper
{
    /**
    * Check wether SSL Certificate is valid and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->is_ssl_valid
    * @return array
    */

    public static function checkSsl(object $data)
    {
        // Array(s) containing message(s) and criticity level(s) that will be returned under conditions
        $errorData = [
            'message' => "Ce site n'est pas correctement securisé.",
            'criticity' => 2
            ];

        // If the website hasn't a valid SSL certificate it's an error (criticity = 2)
        if (isset($data->is_ssl_valid) && $data->is_ssl_valid == 0) {
                return $errorData;
        } else {
            // If the website has a valid SSL certificate no message and critity level null
            return ['message' => '', 'criticity' => 0];
        }
    }


    /**
    * Check wether on_construct is active and associated site link exists,
    * and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['on_construct']
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['on_construct_link']
    * @return array
    */

    public static function checkOnConstruct(object $data)
    {
        // Array(s) containing message(s) and criticity level(s) that will be returned under conditions
        $errorData1 = [
            'message' => "Ce site est en construction mais ne possède pas de lien.",
            'criticity' => 3
            ];

        $errorData2 = [
            'message' => "Ce site est en construction.",
            'criticity' => 2
            ];

        // Convert JSON into PHP variable
        $data = json_decode($data->wp_ext_datas, true);

        // If the website is under construction it's an error (criticity = 2)
        // and without corresponding link it's a more critical error (criticity = 3)
        if (isset($data['on_construct']) && $data['on_construct'] == 1) {
            if (isset($data['on_construct_link']) && empty($data['on_construct_link'])) {
                return $errorData1;
            } else {
                return  $errorData2;
            }
        } else {
            // If the website is not under construction no message and critity level null
            return ['message' => '', 'criticity' => 0];
        }
    }


    /**
    * Check wether wpconfig_debug_log and wpconfig_debug are active and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['wpconfig_debug']
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['wpconfig_debug_log']
    * @return array
    */

    public static function checkWpDebugModeLog(object $data)
    {
        // Array(s) containing message(s) and criticity level(s) that will be returned under conditions
        $errorData1 = [
            'message' => "Le mode debug de ce site WordPress est activé.",
            'criticity' => 2
            ];

        $errorData2 = [
            'message' => "Les modes debug et log de ce site WordPress sont activés.",
            'criticity' => 3
            ];

        // Convert JSON into PHP variable
        $data = json_decode($data->wp_ext_datas, true);

        // If for this website the debug mode is activated it's an error (criticity = 2)
        // and if the log mode is also activated it's a more critical error (criticity = 3)
        if (isset($data['wpconfig_debug']) && $data['wpconfig_debug'] == true) {
            if (isset($data['wpconfig_debug_log']) && ($data['wpconfig_debug_log'] == true)) {
                return $errorData1;
            } else {
                return $errorData2;
            }
        } else {
            // Without debug nore log modes activated no message and critity level null
            return ['message' => '', 'criticity' => 0];
        }
    }


    /**
    * Check wether wpconfig_debug and write_log_fn are active and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['wpconfig_debug_log']
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['write_log_fn']
    * @return array
    */

    public static function checkWpModeLogFn(object $data)
    {
        // Array(s) containing message(s) and criticity level(s) that will be returned under conditions
        $errorData = [
            'message' => "Le mode log de ce site WordPress n'est pas activé mais la fonction d'écriture associée est activée.",
            'criticity' => 3
            ];

        // Convert JSON into PHP variable
        $data = json_decode($data->wp_ext_datas, true);

        // If for this website the log mode is not activated but the associated write mode is
        // it's a critical error (criticity = 3)
        if ((isset($data['wpconfig_debug_log']) && $data['wpconfig_debug_log'] == false) && (isset($data['write_log_fn']) && ($data['write_log_fn'] == 1))) {
            return $errorData;
        } else {
            // Without log nore write modes activated no message and critity level null
            return ['message' => '', 'criticity' => 0];
        }
    }


    /**
    * Check wether GA script is active in footer and GA id exists and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['ga_infooter']
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['ga_infooter_id']
    * @return array
    */

    public static function checkGaScriptId(object $data)
    {
        // Array(s) containing message(s) and criticity level(s) that will be returned under conditions
        $errorData1 = [
            'message' => "Le script GA est activé.",
            'criticity' => 0
            ];

        $errorData2 = [
            'message' => "Le script GA est activé mais il n'y a pas d'id GA.",
            'criticity' => 3
            ];

        // Convert JSON into PHP variable
        $data = json_decode($data->wp_ext_datas, true);

        // If for this website the Google Analytics script is activated in the footer
        // but there's no Google Analytics id it's a critical error (criticity = 3)
        if (isset($data['ga_infooter']) && $data['ga_infooter'] == 1) {
            if (isset($data['ga_infooter_id']) && ($data['ga_infooter_id'] == 0)) {
                return $errorData2;
            } else {
                return $errorData1;
            }
        } else {
            // With both Google Analytics script and id no message and critity level null
            return ['message' => '', 'criticity' => 0];
        }
    }


    /**
    * Check wether WordPress cron is off and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['wpconfig_disablewpcron']
    * @return array
    */

    public static function checkWpCron(object $data)
    {
        // Array(s) containing message(s) and criticity level(s) that will be returned under conditions
        $errorData = [
            'message' => "Le cron de ce site WordPress est désactivé.",
            'criticity' => 1
            ];

        // Convert JSON into PHP variable
        $data = json_decode($data->wp_ext_datas, true);

        // If for this website Wordpress CRON is not activated it's a warning (criticity = 1)
        if (isset($data['wpconfig_disablewpcron']) && $data['wpconfig_disablewpcron'] == 0) {
                return $errorData;
        } else {
            // If Wordpress CRON is activated no message and critity level null
            return ['message' => '', 'criticity' => 0];
        }
    }


    /**
    * Check wether WordPress auto update is active and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->wp_ext_datas['wpconfig_automaticupdater']
    * @return array
    */

    public static function checkWpAutoUpdate(object $data)
    {
        // Array(s) containing message(s) and criticity level(s) that will be returned under conditions
        $errorData = [
            'message' => "La mise a jour automatique de ce site WordPress est activée.",
            'criticity' => 2
            ];

        // Convert JSON into PHP variable
        $data = json_decode($data->wp_ext_datas, true);

        // If for this website Wordpress automatic update is activated it's an error (criticity = 2)
        if (isset($data['wpconfig_automaticupdater']) && $data['wpconfig_automaticupdater'] == 1) {
                return $errorData;
        } else {
            // If Wordpress automatic update is not activated no message and critity level null
            return ['message' => '', 'criticity' => 0];
        }
    }


    /**
    * Check get_header response and return error message and criticity
    *
    * @param App\Models\SupervisionDatas $data->get_header_response['0']
    * @return array
    */

    public static function checkGetHeaderResponse(object $data)
    {
        // Convert JSON into PHP variable
        $data = json_decode($data->get_header_response, true);

        // Return no message and criticity level null if get_header response status is 200
        if (isset($data['0'])) {
            if (str_contains($data['0'], '200')) {
                return [
                    'message' => "",
                    'criticity' => 0
                    ];
            // If get_header response status is 301 it's an error (criticity = 2)
            } elseif (str_contains($data['0'], '301')) {
                return [
                    'message' => "Code de reponse HTTP 301, l'URL fait l'objet d'une redirection permanente vers : " . $data['Location'],
                    'criticity' => 2
                    ];
            // If get_header response status is 302 it's an error (criticity = 2)
            } elseif (str_contains($data['0'], '302')) {
                return [
                    'message' => "Code de reponse HTTP 302, l'URL fait l'objet d'une redirection temporaire vers : " . $data['Location'],
                    'criticity' => 2
                    ];
            // For all other get_header response status it's a more critical error (criticity = 3)
            } else {
                return ['message' => 'Code de reponse : ' . $data['0'], 'criticity' => 3];
            }
        // If get_header response status does not exist no message and criticity level null
        } else {
            return ['message' => '', 'criticity' => 0];
        }
    }
}
