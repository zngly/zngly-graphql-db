<?php

namespace Zngly\Graphql\Db\Model;

// https://www.w3schools.com/sql/sql_datatypes.asp
class FieldType
{

    public $type = "TEXT";
    public $graphql_type = "String";
    public $size;

    public function __construct()
    {
    }

    public static function create()
    {
        return new self;
    }

    private static function size_check($value, $allowed_size)
    {
        if (strlen($value) > $allowed_size) {
            throw new \Exception("Field size is too big, max allowed size is " . $allowed_size);
        }
    }

    private static function signed_prefix(bool $signed)
    {
        return $signed ? " SIGNED" : " UNSIGNED";
    }

    /**
     * @var string 
     * A VARIABLE length string (can contain letters, numbers, and special characters). 
     * The size parameter specifies the maximum column length in characters - can be from 0 to 65535
     */
    public function VARCHAR(int $size = 65535)
    {
        self::size_check($size, 65535);

        $this->type = "VARCHAR($size)";
        $this->graphql_type = "String";
        $this->size = $size;
        return $this;
    }

    /**
     * Holds a string with a maximum length of 255 characters
     */
    public function TINYTEXT()
    {
        $this->type = "TINYTEXT";
        $this->graphql_type = "String";
        $this->size = 255;
        return $this;
    }

    /**
     * Holds a string with a maximum length of 65,535 bytes
     */
    public function TEXT(int $size = 65535)
    {
        self::size_check($size, 65535);

        $this->type = "TEXT";
        $this->graphql_type = "String";
        $this->size = $size;
        return $this;
    }

    /**
     * Holds a string with a maximum length of 16,777,215 characters
     */
    public function MEDIUMTEXT()
    {
        $this->type = "MEDIUMTEXT";
        $this->graphql_type = "String";
        $this->size = 16777215;
        return $this;
    }

    /**
     * Holds a string with a maximum length of 4,294,967,295 characters
     */
    public function LONGTEXT()
    {
        $this->type = "LONGTEXT";
        $this->graphql_type = "String";
        $this->size = 4294967295;
        return $this;
    }

    /**
     * A very small integer. Signed range is from -128 to 127. Unsigned range is from 0 to 255. 
     * The size parameter specifies the maximum display width (which is 255)
     */
    public function TINYINT(bool $signed = false)
    {
        $this->type = "TINYINT(3)" . self::signed_prefix($signed);
        $this->graphql_type = "Int";
        return $this;
    }

    /**
     * Zero is considered as false, nonzero values are considered as true.
     */
    public function BOOL()
    {
        $this->type = "BOOL";
        $this->graphql_type = "Boolean";
        return $this;
    }

    /**
     * Boolean - Equal to BOOL
     */
    public function BOOLEAN()
    {
        $this->type = "BOOLEAN";
        $this->graphql_type = "Boolean";
        return $this;
    }

    /**
     * A small integer. Signed range is from -32768 to 32767. 
     * Unsigned range is from 0 to 65535. The size parameter specifies the maximum display width (which is 255)
     */
    public function SMALLINT(bool $signed = false)
    {
        $this->type = "SMALLINT(5)" . self::signed_prefix($signed);
        $this->graphql_type = "Int";
        return $this;
    }

    /**
     * A medium integer. Signed range is from -8388608 to 8388607. 
     * Unsigned range is from 0 to 16777215. 
     * The size parameter specifies the maximum display width (which is 255)
     */
    public function MEDIUMINT(bool $signed = false)
    {
        $this->type = "MEDIUMINT(7)" . self::signed_prefix($signed);
        $this->graphql_type = "Int";
        return $this;
    }

    /**
     * A medium integer. Signed range is from -2147483648 to 2147483647. 
     * Unsigned range is from 0 to 4294967295. 
     * The size parameter specifies the maximum display width (which is 255)
     */
    public function INT(bool $signed = false)
    {
        $this->type = "INT(11)" . self::signed_prefix($signed);
        $this->graphql_type = "Int";
        return $this;
    }

    /**
     * INTEGER -Equal to INT(size)
     */
    public function INTEGER(int $size = 20, bool $signed = false)
    {
        $this->type = "INTEGER(11)" . self::signed_prefix($signed);
        $this->graphql_type = "Int";
        $this->size = $size;
        return $this;
    }

    /**
     * A large integer. Signed range is from -9223372036854775808 to 9223372036854775807. 
     * Unsigned range is from 0 to 18446744073709551615. 
     * The size parameter specifies the maximum display width (which is 255)
     */
    public function BIGINT(bool $signed = false)
    {
        $this->type = "BIGINT(20)" . self::signed_prefix($signed);
        $this->graphql_type = "Int";
        return $this;
    }

    /**
     * A floating point number. MySQL uses the p value to determine whether 
     * to use FLOAT or DOUBLE for the resulting data type. 
     * If p is from 0 to 24, the data type becomes FLOAT(). 
     * If p is from 25 to 53, the data type becomes DOUBLE()
     */
    public function FLOAT(int $d = 0)
    {
        $this->type = "FLOAT($d)";
        $this->graphql_type = "Float";
        return $this;
    }

    /**
     * A normal-size floating point number. The total number of digits is specified in size. 
     * The number of digits after the decimal point is specified in the d parameter
     */
    public function DOUBLE(int $d = 0)
    {
        $this->type = "DOUBLE(20,$d)";
        $this->graphql_type = "Float";
        return $this;
    }

    /**
     * 	An exact fixed-point number. The total number of digits is specified in size. 
     * The number of digits after the decimal point is specified in the d parameter. 
     * The maximum number for size is 65. The maximum number for d is 30. 
     * The default value for size is 10. The default value for d is 0.
     */
    public function DECIMAL(int $d = 0)
    {
        $this->type = "DECIMAL(20,$d)";
        $this->graphql_type = "Float";
        return $this;
    }

    /**
     * A date. Format: YYYY-MM-DD. The supported range is from '1000-01-01' to '9999-12-31'
     */
    public function DATE()
    {
        $this->type = "DATE";
        $this->graphql_type = "String";
        return $this;
    }
    /**
     * A date and time combination. Format: YYYY-MM-DD hh:mm:ss. 
     * The supported range is from '1000-01-01 00:00:00' to '9999-12-31 23:59:59'. 
     * Adding DEFAULT and ON UPDATE in the column definition to get automatic initialization and 
     * updating to the current date and time
     */
    public function DATETIME(string $value = null)
    {
        $this->type = isset($value) ? "DATETIME($value)" : "DATETIME";
        $this->graphql_type = "String";
        return $this;
    }

    /**
     * TIMESTAMP values are stored as the number of seconds since the 
     * Unix epoch ('1970-01-01 00:00:00' UTC). Format: YYYY-MM-DD hh:mm:ss. 
     * The supported range is from '1970-01-01 00:00:01' UTC to '2038-01-09 03:14:07' UTC. 
     * Automatic initialization and updating to the current date and time can be specified
     * using DEFAULT CURRENT_TIMESTAMP and ON UPDATE CURRENT_TIMESTAMP in the column definition
     */
    public function TIMESTAMP(string $value = null)
    {
        $this->type = isset($value) ? "TIMESTAMP($value)" : "TIMESTAMP";
        $this->graphql_type = "String";
        return $this;
    }

    /**
     * A time. Format: hh:mm:ss. The supported range is from '-838:59:59' to '838:59:59'
     */
    public function TIME(string $value = null)
    {
        $this->type = isset($value) ? "TIME($value)" : "TIME";
        $this->graphql_type = "String";
        return $this;
    }
}
