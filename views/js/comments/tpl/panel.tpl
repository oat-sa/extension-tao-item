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
        <label class="item-comments-label" for="item-comments-input">{{__ 'Add a comment'}}</label>
        <textarea
            id="item-comments-input"
            class="item-comments-input"
            name="comment"
            rows="4"
            placeholder="{{__ 'Add a comment'}}"
        ></textarea>
        <button type="submit" class="btn-info small item-comments-submit" disabled>
            {{__ 'Post comment'}}
        </button>
    </form>
    <div class="item-comments-menu-layer" aria-hidden="true"></div>
</div>
