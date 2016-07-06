<?php

namespace rkdev\yandexmaps\Interfaces;

/**
 * EventAggregate interface.
 */
interface EventAggregate {
	/**
	 * @return array
	 */
	public function getEvents();
}