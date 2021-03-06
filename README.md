# xsd2src

Convert an xsd schema to source code of any type and format.

## Installation

```bash
git clone --depth 1 --branch 0.1.0 https://github.com/machinateur/xsd2src ./xsd2src
cd ./xsd2src
composer install
php bin/console xsd2src -h
```

## Usage

```
Description:
  Create source from xsd.

Usage:
  xsd2src [options] [--] <input> <output> <extension> <context> [<view>]

Arguments:
  input                             Set the input pathname.
  output                            Set the output path.
  extension                         Set the file extension.
  context                           Set the context pathname.
  view                              Set the twig view to use. Default to "xsd2src.{$extension}.twig".

Options:
  -i, --initialize|--no-initialize  Decide whether to initialize configuration or not. Default to "false".
  -r, --re                          Set to re-initialize the existing configuration. Default to "false".
  -x, --with=WITH                   Add one or more extra xsd. (multiple values allowed)
  -s, --schema=SCHEMA               Set the schema pathname to validate input. Used for both, {$input} and extra input (using --with flag).
                                    Should point to a local copy of "https://www.w3.org/2001/XMLSchema.xsd".
  -z, --zip=ZIP                     Decide whether to compress the output as archive or not, the value is the name of the archive.

Options (Symfony related):
  -h, --help                        Display help for the given command. When no command is given display help for the list command
  -q, --quiet                       Do not output any message
  -V, --version                     Display this application version
      --ansi|--no-ansi              Force (or disable --no-ansi) ANSI output
  -n, --no-interaction              Do not ask any interactive question
  -e, --env=ENV                     The Environment name. [default: "dev"]
      --no-debug                    Switch off debug mode.
  -v|vv|vvv, --verbose              Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

```

## List of supported source type/format

* PHP (7.4) with jms-serializer annotations
* *more coming soon...*

## The web version

Currently, there is no web version of this tool. I hope this will be available some day.

## Documentation

> This is a work-in-progress tool.\
> **Any part may be subject to change!**

### Schema structures and types

The following table describes the supported structure variation:

| Class                       | `tle` | `non-tle` | `element`                   |
|:----------------------------|:-----:|:---------:|:----------------------------|
| `NodeHandleChain`         * |       |           |                             |
| `NodeHandleSchema`       ** |       |           | `schema`                    |
| `NodeHandleComplexType`     | [x]   | [x]       | `complexType`               |
| `NodeHandleComplexContent`  | [ ]   | [x]       | `complexContent`            |
| `NodeHandleSimpleType`      | [x]   | [x]       | `simpleType`                |
| `NodeHandleSimpleContent`   | [ ]   | [x]       | `simpleContent`             |
| `NodeHandleExtension`       | [ ]   | [x]       | `extension`                 |
| `NodeHandleRestriction`     | [ ]   | [x]       | `restriction`               |
| `NodeHandleElement`         | [x]   | [x]       | `element`                   |
| `NodeHandleAttribute`       | [ ]   | [x]       | `attribute`                 |
| `NodeHandleIndicator`       | [ ]   | [x]       | `all` `choice` `sequence`   |
| `NodeHandleGroup`           | [x]   | [x]       | `group`                     |
| `NodeHandleAttributeGroup`  | [x]   | [x]       | `attributeGroup`            |

* *: The `NodeHandleChain` is a generic internal handle. It does not process any element, but delegates the call to 
  its own list of `NodeHandleInterface` instances.
* **: The `NodeHandleSchema` can only be `tle`. Since it is the entrypoint of the processing chain, which can handle
  the `schema` element, it must be present in any case.


* `tle` = top level element

### Useful read

* [Understanding W3C Schema Complex Types](https://www.xml.com/pub/a/2001/08/22/easyschema.html)
* [XML Schema Tutorial](https://www.w3schools.com/xml/schema_intro.asp)

## License

It's MIT.
