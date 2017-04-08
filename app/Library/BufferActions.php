<?php namespace App\Library;

use App\MeasurementTags;
use App\Measurements;
use App\Sets as Set;
use App\MSets;
use Carbon\Carbon;

trait BufferActions {

    /**
     * Populate sets
     *
     * @param      <type>  $package  The package
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function populateSets($package)
    {
    	$diffMins = Carbon::parse($package->created_at)->diff(
    		Measurements::find((int)Measurements::max('id') - 1)->created_at
    	)->i;

    	if ($diffMins >= self::DSETS) {
    		// new set and insert it
  			return self::addToSet($package->id, self::createSet()->id)->id;
  		}
    	// get last set and insert it to it
    	return self::addToSet($package->id, self::getLastSetId())->id;
    }

    /**
     * Creates a set.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function createSet()
    {
    	 return Set::create(['is_active' => 1]);
    }

    /**
     * Adds to set.
     *
     * @param      <type>  $mId    The m identifier
     * @param      <type>  $setId  The set identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function addToSet($mId, $setId)
    {
    	 return MSets::create(['m_id' => $mId, 'set_id' => $setId]);
    }

    /**
     * Gets the last set identifier.
     *
     * @return     <type>  The last set identifier.
     */
    public static function getLastSetId()
    {		
    	if (Set::max('id') == null) {
    			self::createSet();
    	}
    	return Set::max('id');
    }
}
