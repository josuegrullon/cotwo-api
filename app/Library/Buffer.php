<?php namespace App\library;

use App\MeasurementTags;
use App\Measurements;
use App\Library\Helpers;

class Buffer extends Helpers{
	
	use BufferActions;


	const DSETS = 1; // distance in minutes between events to be considered as new or existent set
}