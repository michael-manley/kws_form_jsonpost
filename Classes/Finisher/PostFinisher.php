<?php

declare(strict_types = 1);

namespace Bkindler\KwsFormJsonpost\Finisher;

/*
 * This file is part of the "kws_form_jsonpost" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;

class PostFinisher extends \TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher
{
    /**
     * @return string|null
     */
    protected function executeInternal()
    {
        //Get Options
        $url = $this->parseOption('url');
        $username = $this->parseOption('username');
        $password = $this->parseOption('password');
        $variables = $this->parseOption('variables');
        $headerfields = $this->parseOption('headers');
        $fieldKeyAsInteger = $this->parseOption('fieldKeyAsInteger');
        $requestMethod = $this->parseOption('requestMethod');
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        // Initialize postfields array
        $postfields = [];

        // Initialize Headers array
        $headers = ['Content-Type: application/json'];

        // If username/password is set, add to postfields
        if ($username) {
            $postfields['username'] = $username;
        }
        if ($password) {
            $postfields['password'] = $password;
        }

        // Add variables to postfields
        if (!empty($variables)) {
            foreach ($variables as $key => $value) {
                $postfields[$key] = $value;
            }
        }

        // Add headers
        if (!empty($headerfields)) {
            foreach ($headerfields as $key => $value) {
                $headers[] = "{$key}: {$value}";
                $logger->info("Header Test: {$key}: {$value}");
            }
        }

        // Add form values to postfields
        $formValues = $this->finisherContext->getFormValues();

        if ($fieldKeyAsInteger) {
            $formValues = $this->convertFieldKeyToInteger($formValues);
        }

        $postfields['fields'] = $formValues;

        // Convert postfields to JSON
        $jsonPostfields = json_encode($postfields);

        // cURL setup
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($requestMethod == 'POST') {
            curl_setopt($ch, CURLOPT_POST, TRUE);
        }

        // Set the JSON data and headers
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostfields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Execute cURL request and capture the result
        $result = curl_exec($ch);

         $logger->info("Result: {$result}");

        return null;
    }

    /**
     * @return array
     **/
    private function convertFieldKeyToInteger($formValues): array
    {
        $convertedFormValues = [];

        foreach ($formValues as $key => $value) {
            $key = (int) preg_replace('/[^0-9]/', '', $key);
            $convertedFormValues[$key] = $value;
        }

        return $convertedFormValues;
    }
}
