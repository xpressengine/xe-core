<?php
/* Copyright (C) XEHub <https://www.xehub.io> */

/**
 * SortArgument class
 * @author XEHub (developers@xpressengine.com)
 * @package /classes/xml/xmlquery/argument
 * @version 0.1
 */
class SortArgument extends Argument
{

	function getValue()
	{
		return $this->getUnescapedValue();
	}

	function ensureValidColumnName($default_value = NULL)
	{
		if(!isset($this->value) || $this->value === '')
		{
			return;
		}

		if($this->isValidColumnName($this->value))
		{
			return;
		}

		if(func_num_args() > 0)
		{
			$this->value = $default_value;
			$this->uses_default_value = TRUE;
			$this->isValid = TRUE;
			$this->errorMessage = NULL;
			return;
		}

		global $lang;
		$key = $this->name;
		$this->isValid = FALSE;
		$this->errorMessage = new BaseObject(-1, sprintf($lang->filter->invalid, $lang->{$key} ? $lang->{$key} : $key));
	}

	function isValidColumnName($column_name)
	{
		if(!is_string($column_name))
		{
			return FALSE;
		}

		return preg_match('/^[A-Za-z_][A-Za-z0-9_]*(?:\.[A-Za-z_][A-Za-z0-9_]*)?$/', $column_name) === 1;
	}

}
/* End of file SortArgument.class.php */
/* Location: ./classes/xml/xmlquery/argument/SortArgument.class.php */
