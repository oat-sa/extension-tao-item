<span class="icon icon-warning"></span> <b>{{__ 'Warning'}}</b><br><br>
{{__ 'The class'}} <b>{{name}}</b> {{__ 'contains items currently in use and they cannot be deleted.'}}
{{__ 'There is'}} <b>{{number}}</b> {{__ 'items in use:'}}{{#if multiple}}{{/if}}<br>
<ul>
{{#each tests}}<li>{{this.label}}</li>{{/each}}
</ul>
{{#if numberOther}}
<span class="gray-others">{{__ 'and'}} {{numberOther}} {{__ 'other'}}{{#if multipleOthers}}{{__ 's'}}{{/if}}.</span><br><br>
{{/if}}
