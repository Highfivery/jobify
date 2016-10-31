<?php

 /** 
 * Access Careerjet's job search from PHP
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 *
 * @package     Careerjet_API
 * @author      Thomas Busch <api@careerjet.com>
 * @copyright   2007-2015 Careerjet Limited
 * @licence     PHP http://www.php.net/license/3_01.txt
 * @version     3.6
 * @link        http://www.careerjet.com/partners/api/php/
 */


 /**
 * Class to access Careerjet's job search API
 *
 * Code example:
 * 
 * <code>
 *
 *  require_once "Careerjet_API.php";
 *  
 *  // Create a new instance of the interface for UK job offers
 *  $cjapi = new Careerjet_API('en_GB');
 *  
 *
 *  // Then call the search methods (see below for parameters)
 *  $result = $cjapi->search( array(
 *                                   'keywords' => 'java manager',
 *                                   'location' => 'London',
 *                                   'affid'    => '0afaf0173305e4b9',
 *                                 )
 *                           );
 *
 *  if ($result->type == 'JOBS') {
 *      echo "Got ".$result->hits." jobs: \n\n";
 *      $jobs = $result->jobs;
 *
 *      foreach ($jobs as &$job) {
 *          echo " URL: ".$job->url."\n";
 *          echo " TITLE: ".$job->title."\n";
 *          echo " LOC:   ".$job->locations."\n";
 *          echo " COMPANY: ".$job->company."\n";
 *          echo " SALARY: ".$job->salary."\n";
 *          echo " DATE:   ".$job->date."\n";
 *          echo " DESC:   ".$job->description."\n";
 *          echo "\n";
 *       }
 *   }
 *  
 *  </code>
 *     
 *
 * @package    Careerjet_API
 * @author     Thomas Busch <api@careerjet.com>
 * @copyright  2007-2015 Careerjet Limited
 * @link       http://www.careerjet.com/partners/api/php/
 */
class Careerjet_API {
  var $locale = '' ;
  var $version = '3.6';
  var $careerjet_api_content = '';

 /**
  * Creates client to Careerjet's API
  *
  * <code>
  *  $cjapi = new Careerjet_API($locale);
  * </code>
  * 
  * Available locales:
  *
  * <pre>
  *   LOCALE     LANGUAGE         DEFAULT LOCATION     CAREERJET SITE
  *   cs_CZ      Czech            Czech Republic       http://www.careerjet.cz
  *   da_DK      Danish           Denmark              http://www.careerjet.dk
  *   de_AT      German           Austria              http://www.careerjet.at
  *   de_CH      German           Switzerland          http://www.careerjet.ch
  *   de_DE      German           Germany              http://www.careerjet.de
  *   en_AE      English          United Arab Emirates http://www.careerjet.ae
  *   en_AU      English          Australia            http://www.careerjet.com.au
  *   en_CA      English          Canada               http://www.careerjet.ca
  *   en_CN      English          China                http://www.career-jet.cn
  *   en_HK      English          Hong Kong            http://www.careerjet.hk
  *   en_IE      English          Ireland              http://www.careerjet.ie
  *   en_IN      English          India                http://www.careerjet.co.in
  *   en_MY      English          Malaysia             http://www.careerjet.com.my
  *   en_NZ      English          New Zealand          http://www.careerjet.co.nz
  *   en_OM      English          Oman                 http://www.careerjet.com.om
  *   en_PH      English          Philippines          http://www.careerjet.ph
  *   en_PK      English          Pakistan             http://www.careerjet.com.pk
  *   en_QA      English          Qatar                http://www.careerjet.com.qa
  *   en_SG      English          Singapore            http://www.careerjet.sg
  *   en_GB      English          United Kingdom       http://www.careerjet.com
  *   en_US      English          United States        http://www.careerjet.com
  *   en_ZA      English          South Africa         http://www.careerjet.co.za
  *   en_TW      English          Taiwan               http://www.careerjet.com.tw 
  *   en_VN      English          Vietnam              http://www.careerjet.vn
  *   es_AR      Spanish          Argentina            http://www.opcionempleo.com.ar
  *   es_BO      Spanish          Bolivia              http://www.opcionempleo.com.bo
  *   es_CL      Spanish          Chile                http://www.opcionempleo.cl
  *   es_CR      Spanish          Costa Rica           http://www.opcionempleo.co.cr
  *   es_DO      Spanish          Dominican Republic   http://www.opcionempleo.com.do
  *   es_EC      Spanish          Ecuador              http://www.opcionempleo.ec
  *   es_ES      Spanish          Spain                http://www.opcionempleo.com
  *   es_GT      Spanish          Guatemala            http://www.opcionempleo.com.gt
  *   es_MX      Spanish          Mexico               http://www.opcionempleo.com.mx
  *   es_PA      Spanish          Panama               http://www.opcionempleo.com.pa
  *   es_PE      Spanish          Peru                 http://www.opcionempleo.com.pe
  *   es_PR      Spanish          Puerto Rico          http://www.opcionempleo.com.pr
  *   es_PY      Spanish          Paraguay             http://www.opcionempleo.com.py
  *   es_UY      Spanish          Uruguay              http://www.opcionempleo.com.uy
  *   es_VE      Spanish          Venezuela            http://www.opcionempleo.com.ve
  *   fi_FI      Finnish          Finland              http://www.careerjet.fi
  *   fr_CA      French           Canada               http://www.option-carriere.ca
  *   fr_BE      French           Belgium              http://www.optioncarriere.be
  *   fr_CH      French           Switzerland          http://www.optioncarriere.ch
  *   fr_FR      French           France               http://www.optioncarriere.com
  *   fr_LU      French           Luxembourg           http://www.optioncarriere.lu
  *   fr_MA      French           Morocco              http://www.optioncarriere.ma
  *   hu_HU      Hungarian        Hungary              http://www.careerjet.hu
  *   it_IT      Italian          Italy                http://www.careerjet.it
  *   ja_JP      Japanese         Japan                http://www.careerjet.jp
  *   ko_KR      Korean           Korea                http://www.careerjet.co.kr
  *   nl_BE      Dutch            Belgium              http://www.careerjet.be
  *   nl_NL      Dutch            Netherlands          http://www.careerjet.nl
  *   no_NO      Norwegian        Norway               http://www.careerjet.no
  *   pl_PL      Polish           Poland               http://www.careerjet.pl
  *   pt_PT      Portuguese       Portugal             http://www.careerjet.pt
  *   pt_BR      Portuguese       Brazil               http://www.careerjet.com.br
  *   ru_RU      Russian          Russia               http://www.careerjet.ru
  *   ru_UA      Russian          Ukraine              http://www.careerjet.com.ua
  *   sv_SE      Swedish          Sweden               http://www.careerjet.se
  *   sk_SK      Slovak           Slovakia             http://www.careerjet.sk
  *   tr_TR      Turkish          Turkey               http://www.careerjet.com.tr
  *   uk_UA      Ukrainian        Ukraine              http://www.careerjet.ua
  *   vi_VN      Vietnamese       Vietnam              http://www.careerjet.com.vn
  *   zh_CN      Chinese          China                http://www.careerjet.cn
  * </pre>
  *
  */

  function Careerjet_API( $locale = 'en_GB' )
  {
    $this->locale = $locale;
  }

  /**
   * @ignore
   **/

  function call($fname , $args)
  {
    $url = 'http://public.api.careerjet.net/'.$fname.'?locale_code='.$this->locale;

    if (empty($args['affid'])) {
      return (object) array(
        'type' => 'ERROR',
        'error' => "Your Careerjet affiliate ID needs to be supplied. If you don't " .
                   "have one, open a free Careerjet partner account."
      );
    }

    foreach ($args as $key => $value) {
      $url .= '&'. $key . '='. urlencode($value);
    }

    if (empty($_SERVER['REMOTE_ADDR'])) {
      return (object) array(
        'type' => 'ERROR',
        'error' => 'not running within a http server'
      );
    }

    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
      $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      // For more info: http://en.wikipedia.org/wiki/X-Forwarded-For
      $ip = trim(array_shift(array_values(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']))));
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    $url .= '&user_ip=' . $ip;
    $url .= '&user_agent=' . urlencode($_SERVER['HTTP_USER_AGENT']);
    
    // determine current page
    $current_page_url = '';
    if (!empty ($_SERVER["SERVER_NAME"]) && !empty ($_SERVER["REQUEST_URI"])) {
      $current_page_url = 'http';
      if (!empty ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $current_page_url .= "s";
      }
      $current_page_url .= "://";
  
      if (!empty ($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
        $current_page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
      } else {
        $current_page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
      }
    }

    $header = "User-Agent: careerjet-api-client-v" . $this->version . "-php-v" . phpversion();
    if ($current_page_url) {
      $header .= "\nReferer: " . $current_page_url;
    }

    $careerjet_api_context = stream_context_create(array(
      'http' => array('header' => $header)
    ));

    $response = file_get_contents($url, false, $careerjet_api_context);
    return json_decode($response);
  }
  
  
  /**
   * Performs a search using Careerjet's public search API
   * 
   * Code example:
   *
   * <code>
   *   $result = $cjapi->search(array(
   *                                   'keywords'   => 'java',
   *                                   'location'   => 'London',
   *                                   'pagesize'   => 10,
   *                                   'affid'      => '0afaf0173305e4b9',
   *                                 )
   *                          );
   * </code>
   * 
   * If the given location is not ambiguous, you can use this object like that:
   *
   * <code>
   *   if ($result->type == 'JOBS') {
   *      echo "Got ".$result->hits." jobs: \n";
   *      echo " On ".$result->pages." pages \n";
   *      $jobs = $result->jobs;
   *
   *      foreach ($jobs as &$job) {
   *          echo " URL: ".$job->url."\n";
   *          echo " TITLE: ".$job->title."\n";
   *          echo " LOC:   ".$job->locations."\n";
   *          echo " COMPANY: ".$job->company."\n";
   *          echo " SALARY: ".$job->salary."\n";
   *          echo " DATE:   ".$job->date."\n";
   *          echo " DESC:   ".$job->description."\n";
   *          echo " SITE:   ".$job->site."\n";
   *          echo "\n" ;
   *       }
   *   }
   * </code>
   *
   * If the given location is ambiguous, result contains a list of suggested locations:
   *
   * <code>
   *   if ($result->type == 'LOCATIONS') {
   *       echo "Suggested locations:\n";
   *       $locations = $result->locations;
   *       
   *       foreach ($locations as &$loc) {
   *           echo $loc."\n" ;
   *       }
   *   }
   * </code>
   *
   * @param   array  $args
   *
   * map of search parameters
   *
   * Example: array( 'keywords' => 'java manager',
   *                 'location' => 'london', ... );
   *
   * All values of keys MUST be encoded either in ASCII or UTF8.
   * If you use this API within a webpage, make sure:
   *   - That your pages are served in utf-8 encoding OR
   *   - Your job search form begins like that :
   *        <form accept-charset="UTF-8"
   *
   *
   * MANDATORY PARAMETERS
   *
   * The following parameters is mandatory:
   *  - <b>affid:</b><br>
   *    Affiliate ID provided by Careerjet<br>
   *    Requires to open a Careerjet partner account<br>
   *    http://www.careerjet.com/partners/
   *
   * FILTERS
   *
   * All filters have default values and are not mandatory:
   *  - <b>keywords:</b><br>
   *    Keywords to search in job offers. Example: 'java manager'<br>
   *    Default: none (Returns all offers from default country)
   *  - <b>location:</b><br>
   *    Location to search job offers in. Examples: 'London', 'Paris'<br>
   *    Default: none (Returns all offers from default country)
   *  - <b>sort:</b><br>
   *    Type of sort.<br>
   *    Available values are 'relevance' (default), 'date', and 'salary'.
   *  - <b>start_num:</b><br>
   *    Num of first offer returned in entire result space should be >= 1 and <= Number of hits<br>
   *    Default: 1 
   *  - <b>pagesize:</b><br>
   *    Number of offers returned in one call<br>
   *    Default: 20
   *  - <b>page:</b><br>
   *    Current page number (should be >=1)<br>
   *    If set, will override start_num<br>
   *    The maxumum number of page is given by $result->pages
   *  - <b>contracttype:</b><br>
   *    Character code for contract types:<br>
   *    'p'    - permanent job<br>
   *    'c'    - contract<br>
   *    't'    - temporary<br>
   *    'i'    - training<br>
   *    'v'    - voluntary<br>
   *    Default: none (all contract types)
   *  - <b>contractperiod:</b><br>
   *    Character code for contract contract periods:<br>
   *    'f'     - Full time<br>
   *    'p'     - Part time<br>
   *    Default: none (all contract periods)
   *
   * @return object(stdClass)  An object containing results
   *
   */
  function search($args)
  {
    $result =  $this->call('search' , $args);
    if ($result->type == 'ERROR') {
      trigger_error( $result->error );
    }
    return $result;
  }
}

?>
