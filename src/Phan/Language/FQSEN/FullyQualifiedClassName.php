<?php declare(strict_types=1);
namespace Phan\Language\FQSEN;

use InvalidArgumentException;
use Phan\Language\Type;
use Phan\Language\UnionType;
use Phan\Memoize;

use function preg_match;

/**
 * A Fully-Qualified Class Name
 */
class FullyQualifiedClassName extends FullyQualifiedGlobalStructuralElement
{
    use Memoize;

    /**
     * @return int
     * The namespace map type such as \ast\flags\USE_NORMAL or \ast\flags\USE_FUNCTION
     */
    protected static function getNamespaceMapType() : int
    {
        return \ast\flags\USE_NORMAL;
    }

    /**
     * @return string
     * The canonical representation of the name of the object. Functions
     * and Methods, for instance, lowercase their names.
     * TODO: Separate the function used to render names in phan errors
     *       from the ones used for generating array keys.
     */
    public static function canonicalName(string $name) : string
    {
        return $name;
    }

    /**
     * @return FullyQualifiedClassName
     * A fully qualified class name from the given type
     */
    public static function fromType(Type $type) : FullyQualifiedClassName
    {
        return self::fromFullyQualifiedString(
            $type->asFQSENString()
        );
    }

    const valid_class_regex = '/^\\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*$/';

    /**
     * Asserts that something is a valid class FQSEN in PHPDoc.
     * Use this for FQSENs passed in from the analyzed code.
     */
    public static function isValidClassFQSEN(string $type) : bool
    {
        return preg_match(self::valid_class_regex, $type) > 0;
    }

    /**
     * Parses a FQSEN from a string
     *
     * @param $fully_qualified_string
     * An fully qualified string like '\Namespace\Class'
     *
     * @return static
     *
     * @throws InvalidArgumentException on failure.
     */
    public static function fromFullyQualifiedUserProvidedString(string $fully_qualified_string) : FullyQualifiedClassName
    {
        if (!self::isValidClassFQSEN($fully_qualified_string)) {
            throw new InvalidArgumentException("Invalid class FQSEN '$fully_qualified_string'");
        }
        return self::fromFullyQualifiedString($fully_qualified_string);
    }

    /**
     * @return Type
     * The type of this class
     */
    public function asType() : Type
    {
        return Type::fromFullyQualifiedString(
            $this->__toString()
        );
    }

    /**
     * @return UnionType
     * The union type of just this class type
     */
    public function asUnionType() : UnionType
    {
        return $this->asType()->asUnionType();
    }

    /**
     * @return FullyQualifiedClassName
     * The FQSEN for \stdClass.
     */
    public static function getStdClassFQSEN() : FullyQualifiedClassName
    {
        return self::memoizeStatic(__METHOD__, function () : FullyQualifiedClassName {
            return self::fromFullyQualifiedString("\stdClass");
        });
    }
}
