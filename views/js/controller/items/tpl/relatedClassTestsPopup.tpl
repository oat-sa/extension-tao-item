<span class="icon icon-warning"></span> <b>{{__ 'Warning'}}</b><br><br>
{{__ 'The class'}} <b>{{name}}</b> {{__ 'contains content that is currently in use.'}}
{{__ 'Deleting this class will break the'}} <b>{{number}}</b> {{__ 'test'}}{{#if multiple}}{{__ 's'}}{{/if}} {{__ 'using it:'}}
<ul>
{{#each tests}}<li>{{this.label}}</li>{{/each}}
</ul>
{{#if numberOther}}
<span class="gray-others">{{__ 'and'}} {{numberOther}} {{__ 'other'}}{{#if multipleOthers}}{{__ 's'}}{{/if}}.</span><br><br>
{{/if}}

<b>{{__ 'Are you sure you want to delete this class and all of its content?'}}</b>
