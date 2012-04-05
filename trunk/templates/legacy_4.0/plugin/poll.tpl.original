<vte:template>
    <vte:header type="css" href="{VIVVO_THEME}css/plugin_poll.css" />
    <vte:box module="box_poll_list">
        <vte:template>
            <h1><vte:value select="{LNG_PLUGIN_POLL_VIEW_ALL_POLLS}" /></h1>
            <vte:if test="{poll_list}">
                <vte:foreach item="poll" from="{poll_list}">
                    <vte:box module="box_poll">
                        <vte:params>
                            <vte:param name="search_pid" value="{poll.id}" />
                            <vte:param name="search_status" value="1" />
                        </vte:params>
                        <vte:template>
                            <vte:if test="{answer_list}">
                                <div class="poll_box_holder">
                                    <div class="poll_box_header">
                                        <h2><vte:value select="{poll.name}" /></h2>
                                        <span class="poll_question"><vte:value select="{poll.question}" /></span>
                                    </div>
                                    <div id="poll_answer_list">
                                        <vte:foreach item = "answer" from = "{answer_list}" key="index">
                                            <div class="poll_line">
                                                <span class="poll_answer_title"><vte:value select="{answer.answer}" /></span>
                                                <img src="{VIVVO_THEME}img/poll_bar.gif" style="width:{answer.get_percent|'3'}px;height:15px;" />
                                                (<vte:value select="{answer.vote}" /> <vte:value select="{LNG_PLUGIN_POLL_NUMBER_VOTES}" />)
                                            </div>
                                        </vte:foreach>
                                    </div>
                                </div>    
                            </vte:if>
                        </vte:template>
                    </vte:box>
                </vte:foreach>
            </vte:if>
        </vte:template>
    </vte:box>
<vte:template>