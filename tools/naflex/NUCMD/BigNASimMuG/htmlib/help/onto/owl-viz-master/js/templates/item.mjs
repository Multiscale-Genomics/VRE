<a href="#" {{#editable}}contenteditable="true"{{/editable}}>
{{#isExpandable}}
	<i class="icon-{{#isExpanded}}minus{{/isExpanded}}{{^isExpanded}}plus{{/isExpanded}}"></i>
{{/isExpandable}}
{{name}}
{{#isExpandable}}
	<span class="badge badge-inverse">{{childrenNo}}</span>
{{/isExpandable}}
</a>
{{#isExpandable}}
	<ul class="nav nav-list connectedSortable">
	</ul>
{{/isExpandable}}
