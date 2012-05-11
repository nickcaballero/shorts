<?php
/**
 * 
 * This class encapsulates a log entry row.
 * Used mostly to simplify creation of entries.
 * 
 * The log entry could and should be optimized.
 * - Country codes should be in a seperate table
 * - Timestamp precision could be less precise. Depends on analytics needs
 * - Pruning will definitely need to take place
 * 
 * @author nick
 *
 */
class Short_LogEntry extends Short_Model
{
	/**
	 * Id of entry
	 * 
	 * @var int
	 */
	public $id;
	
	/**
	 * Country code
	 * 
	 * @var string
	 */
	public $country;
	
	/**
	 * Timestamp of entry
	 * 
	 * @var string
	 */
	public $timestamp;
	
	/**
	 * Id of short
	 * 
	 * @var int
	 */
	public $short_id;
	
	public static function getTableName() {
		return 'log';
	}
}