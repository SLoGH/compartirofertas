<?php
/**
 * Interface for caching widget data using Transients API.
 * @see http://codex.wordpress.org/Transients_API 
 */
interface Widget_Interface_Cache
{
	/**
	 * 3600 sec - Hour
	 */
	const EXPIRATION_HOUR = 3600;
	
	/**
	 * 1800 sec - Half an hour
	 */
	const EXPIRATION_HALF_HOUR = 1800;
	
	const DELETE_ALL_CACHE = true;
	
	
	/**
	 * Get unique identifier for widget cached data 
	 */
	function getTransientId();
	
	/**
	 * Reinit cache data 
	 */
	function reinitWidgetCache($instance);
	
	/**
	 * Get cached widget data
	 */
	function getCachedWidgetData();
	
	/**
	 * Number of seconds to keep the cached data before refreshing.  
	 */
	function getExparationTime();
	
	/**
	 * Delete a transient  
	 */
	function deleteWidgetCache();
}
?>
