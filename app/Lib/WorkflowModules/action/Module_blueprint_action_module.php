<?php
include_once APP . 'Model/WorkflowModules/WorkflowBaseModule.php';

class Module_blueprint_action_module extends WorkflowBaseActionModule
{
    public $blocking = false;
    public $disabled = true;
    public $id = 'blueprint-action-module';
    public $name = 'Delete old events/attribute';
	    
    public $description = 'Remove Attributes/Event that have a date/publish_timestamp/timestamp that goes back from X days/Month/years ago ';
 #   public $support_filters = true;
 #   public $expect_misp_core_format = true;
    public $icon = 'shapes';
    public $inputs = 1;
    public $outputs = 1;
    public $params = [];


    public function __construct()
    {
        parent::__construct();
        $this->Attribute = ClassRegistry::init('Attribute');
        $this->Event = ClassRegistry::init('Event');


	$handle = fopen("debug_data.txt", "r+");
	ftruncate($handle, filesize($handle));
	rewind($handle);
	fclose($handle);

        $this->params = [
            [
                'id' => 'scope',
                'label' => __('Scope'),
                'type' => 'select',
                'options' => [
                    'event' => __('Event'),
                    'attribute' => __('Attributes'),
                ],
                'default' => 'event',
            ],
            [
                'id' => 'date',
                'label' => __('Last'),
                'type' => 'input',
                'placeholder' => __("30d, 6m, 2y, $currentDate_str, ..."),
            ],
            [
                'id' => 'date_filter',
                'label' => __('Field'),
                'type' => 'select',
                'options' => [
		    'date' => __('date'),
                    'publish_timestamp' => __('publish_timestamp'),
		    'timestamp' => __('timestamp'),

                ],
                'default' => 'date',
	    ]
	];
	file_put_contents("debug_data.txt", json_encode($this->params), FILE_APPEND);

    }

    #Transform unix timestamp to a Datetime object like 'Y-M-D H:i:s'
    private function unix_timestamp_to_datetime($timestamp)
    {
	  $DateTime = new DateTime();
	  $DateTime->setTimestamp($timestamp);
	  return $DateTime;
    }

    #Transform the relative date (30d, 2m, 3y, etc) to a Datetime object
    private function relative_timestamp_to_datetime($RelativeDate)
    {
	$currentDate = new DateTime('now');
	$DateValue = "P" . strtoupper($DateValue);
	$date_interval = new DateInterval($DateValue);
	$DateTimeValue = $currentDate->sub($date_interval);
	return $DateTimeValue;
    }

    public function exec(array $node, WorkflowRoamingData $roamingData, array &$errors = []): bool
    {
        parent::exec($node, $roamingData, $errors);
        // If $this->blocking == true, returning `false` will stop the execution.

        $params = $this->getParamsWithValues($node);



        $rData = $roamingData->getData();
        $user = $roamingData->getUser();

	$now = new DateTime();
	$publish_timestamp = $rData['Event']['publish_timestamp'];
	$timestamp = $rData['Event']['timestamp'];
	$date_info = array("Event_id" => $rData['Event']['id'],
			   "Event_date" => $rData['Event']['date'],
			   "Publish_timestamp" => $publish_timestamp,
			   "Timestamp" => $timestamp);
#	$DateFilter = $params['date_filter']['value'];
#	if (preg_match('/^([1-9][0-9]*[d,D,m,M,y,Y])$/', $matchinItems[]) == false)
#
#	if (preg_match(
	#file_put_contents("debug_data.txt", "rDATA\n\n", FILE_APPEND);
	#file_put_contents("debug_data.txt", json_encode($rData), FILE_APPEND);
	$matchinItems = '';
	$DateFilter = $params['date_filter']['value'];

	if ($params['scope']['value'] == 'attribute') 
	{
        	$matchingItems = Hash::extract($rData, 'Event._AttributeFlattened.{n}');
		if ($DateFilter != 'timestamp') 
		{
			$errors[] = __("Bad \"date_filter\" value: Only \"timestamp\" field accepeted for \"Attribute\" scope selected.");
			return false;
		}
	}
	else 
	{
		$matchingItems = $rData['Event'];
		//leave the function if the event hasn't been published and the 'date_filer' is set to "publish_timestamp"
		if ($DateFilter == "publish_timestamp" and $matchingItems['publish_timestamp'] == 0)
			return true;
	}

	$DateValue = $params['date_filter']['value'];
	if (preg_match('/^([1-9][0-9]*[d,D,m,M,y,Y])$/', $DateValue) == false)
		$DateValue = relative_timestamp_to_datetime($DateValue)
	else
		$DateValue = "P" . strtoupper($DateValue);
	
	if ($params['scope']['value'] == 'attribute') 
	{
		foreach ($matchingItems as $attribute)
		{
			if ($attribute['timestamp'] <= $DateValue)
				//TODO
				//Delete attribue
		}

	}
	else
	{
		if $
	}
	
	if ($DateFilter == 'timestamp' or $DateFilter == 'publish_timestamp')
	{
	}
	foreach ($matchingItems as $attribute)
	{
		file_put_contents("debug_data.txt", json_encode($attribute['timestamp']), FILE_APPEND);
	}

	#file_put_contents("debug_data.txt", json_encode($matchingItems), FILE_APPEND);

	$DateTimeStr = '';
	if ($params['date_filter']['value'] == 'timestamp' or $params['date_filter']['value'] == 'publish_timestamp')
	{
	  $DateFilterValue = new DateTime();
	  $DateFilterValue = $DateFilterValue->setTimestamp($params['date_filter']['value']);
	  $DateTimeStr = $DateFilterValue->format('Y-m-d H:i:s');
	}
	else
	{

	  #file_put_contents("debug_data.txt", json_encode($date_info['Event_date'] . " 00:00:00"), FILE_APPEND);
	  $DateTime1 = date_create_from_format("Y-m-d H:i:s", $date_info['Event_date'] . " 00:00:00");
	  $DateTimeStr = $DateTime1->format('Y-m-d H:i:s');
	}
#	} catch (Exception $e) {
#            $errors[] = __('Something went wrong. Error returned: %s', $e->getMessage());
#            return false;
#        }


	
	file_put_contents("debug_data.txt", json_encode($date_info), FILE_APPEND);
	file_put_contents("debug_data.txt", json_encode("DateFilterValue = $DateTimeStr"), FILE_APPEND);

	$errors[] = __('Bad input value: only "attribute" or "event" value accepted');
	return false;

       # $result = false;
       # $errors[] = __('Execution stopped');
       # return $result;
    }
}
