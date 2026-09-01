<span class="icon icon-warning"></span> <b>{{__ 'Warning'}}</b><br><br>
{{__ 'The item'}} <b>{{name}}</b> {{__ 'is currently in use.'}}
{{__ 'Deleting this item will break the'}} <b>{{__p "%d test" "%d tests" number}}</b> {{__ 'using it:'}}
<ul>
{{#each tests}}<li>{{this.label}}</li>{{/each}}
</ul>
{{#if numberOther}}
<span class="gray-others">{{__ 'and'}} {{__p "%d other." "%d others." numberOther}}</span><br><br>
{{/if}}
