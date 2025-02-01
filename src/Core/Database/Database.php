<?php

namespace Core\Database;

abstract class Database
{
	public abstract function getConnection();
}