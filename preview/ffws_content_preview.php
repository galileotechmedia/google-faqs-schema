<script type="text/javascript">
    function ffws_decodeText(html) {
        var parser = new DOMParser;
        var dom = parser.parseFromString('<!doctype html><body>' + html, 'text/html');
        return dom.body.textContent;
    };
</script>
<script type="text/template" id="fusion-builder-ffws-content-preview">
    <#
    var question = params.ffws_element_question;
    var answer = typeof params.element_content !== 'undefined' ? params.element_content : '';

    var qtag = params.ffws_question_tag;
    var eleID = params.ffws_question_id !== 'undefined' ? params.ffws_question_id : '';
    var eleClass = params.ffws_question_class !== 'undefined' ? params.ffws_question_class : '';

    var answerHTML = ffws_decodeText(answer);

    #>
    <div class="ffws-faq-element">
            <{{qtag}} id="{{eleID}}" class="{{eleClass}} ffws-question">{{question}}</{{qtag}}>
            {{answerHTML}}
    </div>
    <#
    #>
</script>