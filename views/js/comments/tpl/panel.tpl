<div class="item-comments-panel">
    <div class="item-comments-body">
        <div class="item-comments-list" role="log" aria-live="polite"></div>
        <div class="item-comments-empty" hidden>
            <p class="item-comments-empty-title">{{__ 'No comments yet'}}</p>
            <p class="item-comments-empty-subtitle">{{__ 'Be the first to add a comment'}}</p>
        </div>
    </div>
    <div class="item-comments-error" hidden></div>
    <form class="item-comments-entry" autocomplete="off">
        <label id="item-comments-input-label" class="item-comments-label" for="item-comments-input">{{__ 'Add a comment'}}</label>
        <div class="item-comments-rich-editor" data-role="draft-editor-wrapper">
            <div class="item-comments-rich-toolbar" data-role="draft-toolbar" role="toolbar" aria-label="{{__ 'Comment formatting'}}">
                <button type="button" class="item-comments-rich-tool" data-command="bold" title="{{__ 'Bold'}}" aria-label="{{__ 'Bold'}}"><span class="item-comments-toolbar-icon item-comments-toolbar-icon--bold" aria-hidden="true"></span></button>
                <button type="button" class="item-comments-rich-tool" data-command="italic" title="{{__ 'Italic'}}" aria-label="{{__ 'Italic'}}"><span class="item-comments-toolbar-icon item-comments-toolbar-icon--italic" aria-hidden="true"></span></button>
                <button type="button" class="item-comments-rich-tool" data-command="underline" title="{{__ 'Underline'}}" aria-label="{{__ 'Underline'}}"><span class="item-comments-toolbar-icon item-comments-toolbar-icon--underline" aria-hidden="true"></span></button>
                <button type="button" class="item-comments-rich-tool" data-command="bulletedlist" title="{{__ 'Bulleted list'}}" aria-label="{{__ 'Bulleted list'}}"><span class="item-comments-toolbar-icon item-comments-toolbar-icon--ul" aria-hidden="true"></span></button>
                <button type="button" class="item-comments-rich-tool" data-command="numberedlist" title="{{__ 'Numbered list'}}" aria-label="{{__ 'Numbered list'}}"><span class="item-comments-toolbar-icon item-comments-toolbar-icon--ol" aria-hidden="true"></span></button>
                <button type="button" class="item-comments-rich-tool" data-command="link" title="{{__ 'Link'}}" aria-label="{{__ 'Link'}}"><span class="item-comments-toolbar-icon item-comments-toolbar-icon--link" aria-hidden="true"></span></button>
            </div>
            <div id="item-comments-input" class="item-comments-rich-input" data-role="draft-editor" role="textbox" aria-multiline="true" aria-labelledby="item-comments-input-label"></div>
        </div>
        <button type="submit" class="btn-info small item-comments-submit" disabled>
            {{__ 'Post comment'}}
        </button>
    </form>
</div>
