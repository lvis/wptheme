## Version 2.2.12 - Aug 07, 2017

- **Dropdown** - Dropdown can now have `values` specified in javascript when initializing.This should simplify cases where dropdown contents are contingent on other fields, for example listing sub categories. You can see some [examples here](https://jsfiddle.net/Lb7c5dkz/) and in the [usage section of dropdown docs](https://www.semantic-ui.com/modules/dropdown.html#initializing-with-javascript-only)
- **Dropdown** - Fixed regression that caused sub menu `dropdown` inside `ui menu` to always appear on left edge of dropdown introduced `2.2.11` [#5542](https://github.com/Semantic-Org/Semantic-UI/issues/5542)
- **Dropdown** - Dropdown mutation observers now watch to see if the entire `<select>` DOM node is replaced with a different select, and not just if new `<option>` are added
- **Dropdown** - Fixed an issue where css rule for `focused default text` was not being applied for multiselects [#5633](https://github.com/Semantic-Org/Semantic-UI/issues/5633)
- **Dropdown** - Calling dropdown methods on `<select>` will now work when using `setting` behavior to set settings after load [#3744](https://github.com/Semantic-Org/Semantic-UI/issues/3744)