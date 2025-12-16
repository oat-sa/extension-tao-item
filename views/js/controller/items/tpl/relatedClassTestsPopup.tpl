<span class="icon icon-warning"></span> <b>{{__ 'Warning'}}</b><br><br>
{{__ 'Some items in the folder '}} <b>{{name}}</b> {{__ 'are currently used in one or more tests, so they cannot be deleted:'}}
<ul>
{{#each tests}}<li>{{this.label}}</li>{{/each}}
</ul>
{{#if numberOther}}
<span class="gray-others">{{__ 'and'}} {{numberOther}} {{__ 'other'}}{{#if multipleOthers}}{{__ 's'}}{{/if}}.</span><br><br>
{{/if}}
{{__ 'If you continue:'}}<br>
<ul>
    <li>{{__ 'Only items not used in any test will be deleted.'}}</li>
    <li>{{__ 'You will see a message saying that some items could not be deleted. This is expected.'}}</li>
</ul>
