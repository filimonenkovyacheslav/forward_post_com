<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NewWorksheet;
use SoapClient;
use Mtownsend\XmlToArray\XmlToArray;
use Illuminate\Support\Facades\Storage;


class RuPostalTrackingController extends Controller
{
	private $status_arr = [
		'Принято в отделении связи' => 'На таможне в стране получателя',
		'Отправлено из Германии' => 'На таможне в стране получателя',
		'Прибыло на территорию России' => 'На таможне в стране получателя',
		'Прибыло на территорию РФ' => 'На таможне в стране получателя',
		'Прошло регистрацию' => 'На таможне в стране получателя',
		'Прием на таможню' => 'На таможне в стране получателя',
		'Выпущено таможней' => 'Доставляется в почтовое отделение',
		'Передано в доставку по России' => 'Доставляется в почтовое отделение',
		'Покинуло место международного обмена' => 'Доставляется в почтовое отделение',
		'Прибыло в место вручения' => 'В почтовом отделении получателя',
		'Получено адресатом' => 'Доставлено',
		'Адресату курьером' => 'Доставлено',
		'Адресату почтальоном' => 'Доставлено',
		'Выдано адресату через почтомат' => 'Доставлено',
		'Адресату с контролем ответа' => 'Доставлено',
		'Адресату с контролем ответа почтальоном' => 'Доставлено',
		'Адресату с контролем ответа курьером' => 'Доставлено',
		'Вручение адресату по ПЭП' => 'Доставлено',
		'Адресату Экспедитором' => 'Доставлено',
		'Адресату почтальоном по ПЭП' => 'Доставлено',
		'Адресату курьером по ПЭП' => 'Доставлено',
		'Вручение адресату' => 'Доставлено',
		'Истек срок хранения' => 'Возвращается в сортировочный центр для переадресации',
		'Заявление отправителя' => 'Возвращается в сортировочный центр для переадресации',
		'Отсутствие адресата по указанному адресу' => 'Возвращается в сортировочный центр для переадресации',
		'Отказ адресата' => 'Возвращается в сортировочный центр для переадресации',
		'Смерть адресата' => 'Возвращается в сортировочный центр для переадресации',
		'Невозможно прочесть адрес адресата' => 'Возвращается в сортировочный центр для переадресации',
		'Отказ в выпуске таможней' => 'Возвращается в сортировочный центр для переадресации',
		'Адресат, абонирующий абонементный почтовый шкаф, не указан или указан неправильно' => 'Возвращается в сортировочный центр для переадресации',
		'Иные обстоятельства' => 'Возвращается в сортировочный центр для переадресации',
		'Неверный адрес' => 'Возвращается в сортировочный центр для переадресации',
		'Несоответствие комплектности' => 'Возвращается в сортировочный центр для переадресации',
		'Запрещено САБ' => 'Возвращается в сортировочный центр для переадресации',
		'Для проведения таможенных операций' => 'Возвращается в сортировочный центр для переадресации',
		'Распоряжение ЭТП' => 'Возвращается в сортировочный центр для переадресации',
		'Частичный выкуп' => 'Возвращается в сортировочный центр для переадресации',
		'По согласованию с адресатом' => 'Возвращается в сортировочный центр для переадресации',

		'Прибыла в пункт назначения' => 'В пункте выдачи',
		'В пути - Покинула промежуточный пункт' => 'Доставляется получателю',
		'В пути - Прибыла в промежуточный пункт' => 'Доставляется получателю',
		'Покинула таможню' => 'Доставляется получателю',
		'Прибыла на таможню' => 'На таможне в стране получателя',
		'Импорт в страну назначения' => 'Доставляется в страну получателя',
		'Экспорт из страны отправления' => 'Доставляется в страну получателя',
		'Посылка принята' => 'Доставляется в страну получателя'
	];


	private $status_new = [
		'Доставляется в почтовое отделение' => 'Доставляется получателю',
		'В почтовом отделении получателя' => 'Доставляется получателю'
	];


	private $status_en = [
		'Доставляется в страну получателя' => 'Forwarding to the receiver country',
		'На таможне в стране получателя' => 'At the customs in the receiver country',
		'Доставляется получателю' => 'Forwarding to the receiver',
		'Возвращается в сортировочный центр для переадресации' => 'Being returned to the consolidation spot for readressing',
		'В пункте выдачи' => 'At the destination',
		'Доставлено' => 'Delivered'
	];


	private $status_he = [
		'Доставляется в страну получателя' => " נשלח למדינת המקבל",
		'На таможне в стране получателя' => " במכס במדינת המקבל",
		'Доставляется получателю' => " נמסר למקבל",
		'Возвращается в сортировочный центр для переадресации' => 'בחזרה למרכז מיונים לשם הפניה מחדש',
		'В пункте выдачи' => 'ביעד',
		'Доставлено' => " נמסר"
	];


	private $status_ua = [
		'Доставляется в страну получателя' => 'Доставляється в країну відправника',
		'На таможне в стране получателя' => 'На митниці в країні отримувача',
		'Доставляется получателю' => 'Доставляється отримувачу',
		'Возвращается в сортировочный центр для переадресации' => 'Повертається в сортувальний центр для переадресації',
		'В пункте выдачи' => 'У пункті призначення',
		'Доставлено' => 'Доставлено'
	];
	

	private function getRuPostalStatus($barcode){
		set_time_limit(0);
		$array = [];
		$status = '';
		$wsdlurl = 'https://tracking.russianpost.ru/rtm34?wsdl';
		$login = "avLLJxuLlwJFtC";
		$password = "siirrfSy7W4T";
		$request = '<?xml version="1.0" encoding="UTF-8"?>
		<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:oper="http://russianpost.org/operationhistory" xmlns:data="http://russianpost.org/operationhistory/data" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Header/>
		<soap:Body>
		<oper:getOperationHistory>
		<data:OperationHistoryRequest>
		<data:Barcode>'.$barcode.'</data:Barcode>  
		<data:MessageType>0</data:MessageType>
		<data:Language>RUS</data:Language>
		</data:OperationHistoryRequest>
		<data:AuthorizationHeader soapenv:mustUnderstand="1">
		<data:login>'.$login.'</data:login>
		<data:password>'.$password.'</data:password>
		</data:AuthorizationHeader>
		</oper:getOperationHistory>
		</soap:Body>
		</soap:Envelope>';			

		$client = new SoapClient($wsdlurl,  array('trace' => 1, 'soap_version' => SOAP_1_2));
		$xml_string = $client->__doRequest($request, "https://tracking.russianpost.ru/rtm34", "PostalOrderEventsForMail", SOAP_1_2);
		$array = XmlToArray::convert($xml_string);

		//echo '<pre>'.print_r($barcode,TRUE).'</pre>';
		//echo '<pre>'.print_r($array,TRUE).'</pre>';	
		
		if (isset($array['S:Body']['ns7:getOperationHistoryResponse']['ns3:OperationHistoryData']['ns3:historyRecord']))
		{
			$new_array = $array['S:Body']['ns7:getOperationHistoryResponse']['ns3:OperationHistoryData']['ns3:historyRecord'];
			$quantity = count($new_array) - 1;		
			
			if (isset($new_array[$quantity]['ns3:OperationParameters']['ns3:OperAttr']['ns3:Name'])) {
				$status = $new_array[$quantity]['ns3:OperationParameters']['ns3:OperAttr']['ns3:Name'];
			}
			
			//echo '<pre>'.print_r($status,TRUE).'</pre>';
		}
								
		return $status;
		//return 0;
	}


	public function updateStatus()
	{
		set_time_limit(0);

		$tracking_main = NewWorksheet::where([
			['tracking_main', 'like', 'CD%'],
			['status', '<>', 'Доставлено'],
			['status', '<>', 'Доставляется на склад в стране отправителя'],
			['status', '<>', 'На складе в стране отправителя']
		])
		->orWhere([
			['tracking_main', 'like', '%'.', CD%'],
			['status', '<>', 'Доставлено'],
			['status', '<>', 'Доставляется на склад в стране отправителя'],
			['status', '<>', 'На складе в стране отправителя']
		])
		->orderBy('update_status_date')
		->offset(0)->limit(99)->get();
												
		if ($tracking_main->count()) {
			foreach ($tracking_main as $val) {
				$status = $this->getRuPostalStatus(mb_substr($this->getTracking($val->tracking_main), 0, 13));
				
				if ($status){

					if (array_key_exists($status, $this->status_arr)) {

						$new_status = $this->status_arr[$status];
						NewWorksheet::where([
							['tracking_main', '=', $val->tracking_main]
						])
						->update([
							'status' => $new_status,
							'update_status_date' => date('Y-m-d'),
							'status_date' => date('Y-m-d')
						]);

						if ($new_status === 'Доставляется в почтовое отделение' || $new_status === 'В почтовом отделении получателя') {
							NewWorksheet::where([
								['tracking_main', '=', $val->tracking_main]
							])
							->update([
								'status_en' => $this->status_en[$this->status_new[$new_status]],
								'status_he' => $this->status_he[$this->status_new[$new_status]],
								'status_ua' => $this->status_ua[$this->status_new[$new_status]]
							]);
						}
						else{
							NewWorksheet::where([
								['tracking_main', '=', $val->tracking_main]
							])
							->update([
								'status_en' => $this->status_en[$new_status],
								'status_he' => $this->status_he[$new_status],
								'status_ua' => $this->status_ua[$new_status]
							]);
						}
					}
					else{
						$new_status = 'Доставляется в почтовое отделение';
						NewWorksheet::where([
							['tracking_main', '=', $val->tracking_main]
						])
						->update([
							'status' => $new_status,
							'status_en' => $this->status_en[$this->status_new[$new_status]],
							'status_he' => $this->status_he[$this->status_new[$new_status]],
							'status_ua' => $this->status_ua[$this->status_new[$new_status]],
							'update_status_date' => date('Y-m-d'),
							'status_date' => date('Y-m-d')
						]);
					}
				}
				else{
					NewWorksheet::where([
						['tracking_main', '=', $val->tracking_main]
					])
					->update([
						'update_status_date' => date('Y-m-d H:i:s')
					]);
				}
				sleep(1);
			}
		}

		return redirect()->route('adminIndex');	
	}


	public function updateStatusFromUser($barcode)
	{
		$tracking_main = [];
		
		if ($barcode) {
			$tracking_main = NewWorksheet::where([
				['tracking_main', 'like', $barcode],
				['status', '<>', 'Доставлено'],
				['status', '<>', 'Доставляется на склад в стране отправителя'],
				['status', '<>', 'На складе в стране отправителя']
			])
			->offset(0)->limit(1)->get();
		}
												
		if ($tracking_main->count()) {
			foreach ($tracking_main as $val) {
				$status = $this->getRuPostalStatus(mb_substr($this->getTracking($val->tracking_main), 0, 13));
				
				if ($status){

					if (array_key_exists($status, $this->status_arr)) {

						$new_status = $this->status_arr[$status];
						NewWorksheet::where([
							['tracking_main', '=', $val->tracking_main]
						])
						->update([
							'status' => $new_status,
							'update_status_date' => date('Y-m-d'),
							'status_date' => date('Y-m-d')
						]);

						if ($new_status === 'Доставляется в почтовое отделение' || $new_status === 'В почтовом отделении получателя') {
							NewWorksheet::where([
								['tracking_main', '=', $val->tracking_main]
							])
							->update([
								'status_en' => $this->status_en[$this->status_new[$new_status]],
								'status_he' => $this->status_he[$this->status_new[$new_status]],
								'status_ua' => $this->status_ua[$this->status_new[$new_status]]
							]);
						}
						else{
							NewWorksheet::where([
								['tracking_main', '=', $val->tracking_main]
							])
							->update([
								'status_en' => $this->status_en[$new_status],
								'status_he' => $this->status_he[$new_status],
								'status_ua' => $this->status_ua[$new_status]
							]);
						}
					}
					else{
						$new_status = 'Доставляется в почтовое отделение';
						NewWorksheet::where([
							['tracking_main', '=', $val->tracking_main]
						])
						->update([
							'status' => $new_status,
							'status_en' => $this->status_en[$this->status_new[$new_status]],
							'status_he' => $this->status_he[$this->status_new[$new_status]],
							'status_ua' => $this->status_ua[$this->status_new[$new_status]],
							'update_status_date' => date('Y-m-d'),
							'status_date' => date('Y-m-d')
						]);
					}
				}
				else{
					NewWorksheet::where([
						['tracking_main', '=', $val->tracking_main]
					])
					->update([
						'update_status_date' => date('Y-m-d H:i:s')
					]);
				}
				sleep(1);
			}
		}
	}


	public function cronScript(){
		//$this->updateStatus();
		//$this->getRuPostalStatus('CD018736056RU');
		$this->getRuPostalStatus('CD018738573RU');
		// wget -O - -q -t 1 https://www.ddcargos.com/api/ru-postal-tracking-cron
	}


	private function getTracking($tracking){
		$arr = explode(",", $tracking);
		for ($i=0; $i < count($arr); $i++) { 
			if (strpos($arr[$i], 'CD') !== false) {
				return trim($arr[$i]);
			}
		}
		return 0;		
	}

}


