<template>
    <LoadingComponent :props="loading"/>

    <div id="company" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.site") }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_date_format" class="db-field-title required">{{
                                $t("label.date_format")
                            }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_date_format"
                                    v-bind:class="errors.site_date_format ? 'is-invalid' : ''"
                                    v-model="form.site_date_format"
                                    :options="enums.dateFormatEnum" label-by="name" value-by="id" :closeOnSelect="true"
                                    :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        <small class="db-field-alert" v-if="errors.site_date_format">{{
                                errors.site_date_format[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_time_format" class="db-field-title required">{{
                                $t("label.time_format")
                            }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_time_format"
                                    v-bind:class="errors.site_time_format ? 'is-invalid' : ''"
                                    v-model="form.site_time_format"
                                    :options="enums.timeFormatEnum" label-by="name" value-by="id" :closeOnSelect="true"
                                    :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        <small class="db-field-alert" v-if="errors.site_time_format">{{
                                errors.site_time_format[0]
                            }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_timezone"
                               class="db-field-title required">{{ $t("label.default_timezone") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_default_timezone"
                                    v-bind:class="errors.site_default_timezone ? 'is-invalid' : ''"
                                    v-model="form.site_default_timezone" :options="timezones" label-by="name"
                                    value-by="name"
                                    :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                    search-placeholder="--"/>
                        <small class="db-field-alert"
                               v-if="errors.site_default_timezone">{{ errors.site_default_timezone[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_language"
                               class="db-field-title required">{{ $t("label.default_language") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_default_language"
                                    v-bind:class="errors.site_default_language ? 'is-invalid' : ''"
                                    v-model="form.site_default_language" :options="languages" label-by="name"
                                    value-by="id"
                                    :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                    search-placeholder="--"/>
                        <small class="db-field-alert"
                               v-if="errors.site_default_language">{{ errors.site_default_language[0] }} </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_sms_gateway"
                               class="db-field-title">{{ $t("label.default_sms_gateway") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_default_sms_gateway"
                                    v-bind:class="errors.site_default_sms_gateway ? 'invalid' : ''"
                                    v-model="form.site_default_sms_gateway" :options="smsGateways" label-by="name"
                                    value-by="id"
                                    :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                    search-placeholder="--"/>
                        <small class="db-field-alert"
                               v-if="errors.site_default_sms_gateway">{{ errors.site_default_sms_gateway[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_storage" class="db-field-title required">{{
                                $t("label.default_storage")
                            }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_default_storage"
                                    v-bind:class="errors.site_default_storage ? 'invalid' : ''"
                                    v-model="form.site_default_storage" :options="storages" label-by="name"
                                    value-by="id"
                                    :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                    search-placeholder="--"/>
                        <small class="db-field-alert"
                               v-if="errors.site_default_storage">{{ errors.site_default_storage[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_copyright" class="db-field-title required">{{ $t("label.copyright") }}</label>
                        <input v-model="form.site_copyright" v-bind:class="errors.site_copyright ? 'invalid' : ''"
                               type="text" id="site_copyright" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_copyright">{{
                                errors.site_copyright[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_google_map_key" class="db-field-title required"> {{
                                $t("label.google_map_key")
                            }}</label>
                        <input v-model="form.site_google_map_key"
                               v-bind:class="errors.site_google_map_key ? 'invalid' : ''" type="text"
                               id="site_google_map_key" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_google_map_key">{{
                                errors.site_google_map_key[0]
                            }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_digit_after_decimal_point"
                               class="db-field-title required">{{ $t("label.digit_after_decimal_point") }}<span
                            class="text-primary"> {{ $t("label.ex") }}</span></label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.site_digit_after_decimal_point"
                               v-bind:class="errors.site_digit_after_decimal_point ? 'invalid' : ''" type="text"
                               id="site_digit_after_decimal_point" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_digit_after_decimal_point">
                            {{ errors.site_digit_after_decimal_point[0] }} </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_ai_agent"
                               class="db-field-title required">{{ $t("label.default_ai_agent") }} </label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_default_ai_agent"
                                    v-bind:class="errors.site_default_ai_agent ? 'is-invalid' : ''"
                                    v-model="form.site_default_ai_agent" :options="aiAgents" label-by="name"
                                    value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                    placeholder="--" search-placeholder="--"/>
                        <small class="db-field-alert"
                               v-if="errors.site_default_ai_agent">{{ errors.site_default_ai_agent[0] }} </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_ai_data_generation_limit" class="db-field-title required">{{
                                $t("label.default_ai_data_generation_limit")
                            }}</label>
                        <input v-on:keypress="floatNumber($event)"
                               v-model="form.site_default_ai_data_generation_limit"
                               v-bind:class="errors.site_default_ai_data_generation_limit ? 'invalid' : ''" type="text"
                               id="site_default_ai_data_generation_limit" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_default_ai_data_generation_limit">{{
                                errors.site_default_ai_data_generation_limit[0]
                            }} </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_currency" class="db-field-title required">
                            {{ $t("label.default_currency") }} </label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_default_currency"
                                    v-bind:class="errors.site_default_currency ? 'is-invalid' : ''"
                                    v-model="form.site_default_currency" :options="currencies" label-by="name_symbol"
                                    value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                    placeholder="--"
                                    search-placeholder="--"/>
                        <small class="db-field-alert" v-if="errors.site_default_currency">
                            {{ errors.site_default_currency[0] }} </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="currency_position_left">
                            {{ $t("label.currency_position") }} </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.currencyPositionEnum.LEFT"
                                           v-model="form.site_currency_position" id="currency_position_left"
                                           type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="currency_position_left" class="db-field-label">
                                    ({{ form.site_default_currency_symbol }}) {{ $t("label.left") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.currencyPositionEnum.RIGHT"
                                           v-model="form.site_currency_position" id="currency_position_right"
                                           type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="currency_position_right" class="db-field-label"> {{ $t("label.right") }}
                                    ({{ form.site_default_currency_symbol }}) </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_currency_position">
                            {{ errors.site_currency_position[0] }} </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_order_commission" class="db-field-title required">
                            {{ $t("label.default_order_commission") }} (%)
                            <span class="relative inline-flex flex-col items-center group">
                                <TooltipComponent :text="$t('message.commission_from_restaurant')" line="multiple"
                                                  tag="button" class="flex items-center justify-center gap-2">
                                    <i class="lab-line-info-circle text-md"></i>
                                </TooltipComponent>
                            </span>
                        </label>
                        <input v-on:keypress="floatNumber($event)"
                               v-model="form.site_default_order_commission"
                               v-bind:class="errors.site_default_order_commission ? 'invalid' : ''" type="text"
                               id="site_default_order_commission" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_default_order_commission">
                            {{ errors.site_default_order_commission[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_delivery_commission" class="db-field-title required">
                            {{ $t("label.default_delivery_commission") }} (%)
                            <span class="relative inline-flex flex-col items-center group">
                                <TooltipComponent :text="$t('message.commission_from_delivery_boy')" line="multiple"
                                                  tag="button" class="flex items-center justify-center gap-2">
                                    <i class="lab-line-info-circle text-md"></i>
                                </TooltipComponent>
                            </span>
                        </label>
                        <input v-on:keypress="floatNumber($event)"
                               v-model="form.site_default_delivery_commission"
                               v-bind:class="errors.site_default_delivery_commission ? 'invalid' : ''" type="text"
                               id="site_default_delivery_commission" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_default_delivery_commission">
                            {{ errors.site_default_delivery_commission[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_pos_commission" class="db-field-title required">
                            {{ $t("label.default_pos_commission") }} (%)
                            <span class="relative inline-flex flex-col items-center group">
                                <TooltipComponent :text="$t('message.commission_from_pos')" line="multiple" tag="button"
                                                  class="flex items-center justify-center gap-2">
                                    <i class="lab-line-info-circle text-md"></i>
                                </TooltipComponent>
                            </span>
                        </label>
                        <input v-on:keypress="floatNumber($event)"
                               v-model="form.site_default_pos_commission"
                               v-bind:class="errors.site_default_pos_commission ? 'invalid' : ''" type="text"
                               id="site_default_pos_commission" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_default_pos_commission">
                            {{ errors.site_default_pos_commission[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_service_fee" class="db-field-title required">
                            {{ $t("label.service_fee") }}
                        </label>
                        <input v-model="form.site_service_fee" v-bind:class="errors.site_service_fee ? 'invalid' : ''"
                               type="text" id="site_service_fee" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_service_fee">
                            {{ errors.site_service_fee[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_restaurant_search_radius" class="db-field-title required">
                            {{ $t("label.restaurant_search_radius") }}
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.site_restaurant_search_radius"
                               v-bind:class="errors.site_restaurant_search_radius ? 'invalid' : ''" type="text"
                               id="site_restaurant_search_radius" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_restaurant_search_radius">
                            {{ errors.site_restaurant_search_radius[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_delivery_boy_order_radius" class="db-field-title required">
                            {{ $t("label.delivery_boy_order_radius") }}
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.site_delivery_boy_order_radius"
                               v-bind:class="errors.site_delivery_boy_order_radius ? 'invalid' : ''" type="text"
                               id="site_delivery_boy_order_radius" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_delivery_boy_order_radius">
                            {{ errors.site_delivery_boy_order_radius[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_same_time_delivery_boy_maximum_orders_accept_limit"
                               class="db-field-title required">
                            {{ $t("label.same_time_delivery_boy_maximum_orders_accept_limit") }}
                        </label>
                        <input v-on:keypress="onlyNumber($event)"
                               v-model="form.site_same_time_delivery_boy_maximum_orders_accept_limit"
                               v-bind:class="errors.site_same_time_delivery_boy_maximum_orders_accept_limit ? 'invalid' : ''"
                               type="text" id="site_same_time_delivery_boy_maximum_orders_accept_limit"
                               class="db-field-control"/>
                        <small class="db-field-alert"
                               v-if="errors.site_same_time_delivery_boy_maximum_orders_accept_limit">
                            {{ errors.site_same_time_delivery_boy_maximum_orders_accept_limit[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_rating_time" class="db-field-title required"> {{ $t("label.rating_time") }}
                            <span class="relative inline-flex flex-col items-center group">
                                <TooltipComponent :text="$t('message.rating_time')" line="multiple" tag="button"
                                                  class="flex items-center justify-center gap-2">
                                    <i class="lab-line-info-circle text-md"></i>
                                </TooltipComponent>
                            </span>
                        </label>
                        <input v-on:keypress="onlyNumber($event)" v-model="form.site_rating_time"
                               v-bind:class="errors.site_rating_time ? 'invalid' : ''" type="text" id="site_rating_time"
                               class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_rating_time"> {{
                                errors.site_rating_time[0]
                            }} </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_return_order_time" class="db-field-title required">
                            {{ $t("label.return_order_time") }}
                            <span class="relative inline-flex flex-col items-center group">
                                <TooltipComponent :text="$t('message.return_order_time')" line="multiple" tag="button"
                                                  class="flex items-center justify-center gap-2">
                                    <i class="lab-line-info-circle text-md"></i>
                                </TooltipComponent>
                            </span>
                        </label>
                        <input v-on:keypress="onlyNumber($event)" v-model="form.site_return_order_time"
                               v-bind:class="errors.site_return_order_time ? 'invalid' : ''" type="text"
                               id="site_return_order_time" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.site_return_order_time">
                            {{ errors.site_return_order_time[0] }} </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="cash_on_delivery_enable">
                            {{ $t("label.cash_on_delivery") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_cash_on_delivery"
                                           id="cash_on_delivery_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="cash_on_delivery_enable" class="db-field-label">
                                    {{ $t("label.enable") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_cash_on_delivery"
                                           type="radio" id="cash_on_delivery_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="cash_on_delivery_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_cash_on_delivery">
                            {{ errors.site_cash_on_delivery[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="online_payment_gateway_enable">
                            {{ $t("label.online_payment_gateway") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_online_payment_gateway"
                                           id="online_payment_gateway_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="online_payment_gateway_enable" class="db-field-label">
                                    {{ $t("label.enable") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE"
                                           v-model="form.site_online_payment_gateway" type="radio"
                                           id="online_payment_gateway_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="online_payment_gateway_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_online_payment_gateway">
                            {{ errors.site_online_payment_gateway[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="rider_tip_enable">
                            {{ $t("label.rider_tip") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_rider_tip"
                                           id="rider_tip_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="rider_tip_enable" class="db-field-label">
                                    {{ $t("label.enable") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_rider_tip"
                                           type="radio" id="rider_tip_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="rider_tip_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_rider_tip">
                            {{ errors.site_rider_tip[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="cutlery_enable">
                            {{ $t("label.cutlery") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_cutlery"
                                           id="cutlery_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="cutlery_enable" class="db-field-label">
                                    {{ $t("label.enable") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_cutlery" type="radio"
                                           id="cutlery_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="cutlery_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_cutlery">
                            {{ errors.site_cutlery[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="language_switch_enable">
                            {{ $t("label.language_switch") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_language_switch"
                                           id="language_switch_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="language_switch_enable" class="db-field-label">
                                    {{ $t("label.enable") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_language_switch"
                                           type="radio" id="language_switch_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="language_switch_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_language_switch">
                            {{ errors.site_language_switch[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="email_verification_enable">
                            {{ $t("label.email_verification") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_email_verification"
                                           id="email_verification_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="email_verification_enable" class="db-field-label">
                                    {{ $t("label.enable") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_email_verification"
                                           type="radio" id="email_verification_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="email_verification_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_email_verification">
                            {{ errors.site_email_verification[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="phone_verification_enable">
                            {{ $t("label.phone_verification") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_phone_verification"
                                           id="phone_verification_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="phone_verification_enable" class="db-field-label">
                                    {{ $t("label.enable") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_phone_verification"
                                           type="radio" id="phone_verification_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="phone_verification_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_phone_verification">
                            {{ errors.site_phone_verification[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="app_debug_enable">
                            {{ $t("label.app_debug") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_app_debug"
                                           id="app_debug_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="app_debug_enable" class="db-field-label">{{ $t("label.enable") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_app_debug"
                                           type="radio" id="app_debug_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="app_debug_disable" class="db-field-label">{{ $t("label.disable") }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_app_debug">
                            {{ errors.site_app_debug[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="auto_localization_enable">
                            {{ $t("label.auto_localization") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_auto_localization"
                                           id="auto_localization_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="auto_localization_enable" class="db-field-label">{{ $t("label.enable") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_auto_localization"
                                           type="radio" id="auto_localization_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="auto_localization_disable" class="db-field-label">{{ $t("label.disable") }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_auto_localization">
                            {{ errors.site_auto_localization[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="auto_update_enable">
                            {{ $t("label.auto_update") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_auto_update"
                                           id="auto_update_enable" type="radio" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="auto_update_enable" class="db-field-label">{{ $t("label.enable") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_auto_update"
                                           type="radio" id="auto_update_disable" class="custom-radio-field"/>
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="auto_update_disable" class="db-field-label">
                                    {{ $t("label.disable") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_auto_update">
                            {{ errors.site_auto_update[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 mt-5">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-fill-save text-base"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import dateFormatEnum from "../../../../enums/modules/dateFormatEnum.js";
import timeFormatEnum from "../../../../enums/modules/timeFormatEnum.js";
import activityEnum from "../../../../enums/modules/activityEnum.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import alertService from "../../../../services/alertService.js";
import appService from "../../../../services/appService.js";
import {useSiteStore} from "../../../../stores/site.js";
import {useTimezoneStore} from "../../../../stores/timezone.js";
import {useCurrencyStore} from "../../../../stores/currency.js";
import {useLanguageStore} from "../../../../stores/language.js";
import {useSmsGatewayStore} from "../../../../stores/smsGateway.js";
import {useAiAgentStore} from "../../../../stores/aiAgent.js";
import {useStorageStore} from "../../../../stores/storage.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import currencyPositionEnum from "../../../../enums/modules/currencyPositionEnum.js";
import TooltipComponent from "../../../common/TooltipComponent.vue";

export default {
    name: "SiteComponent",
    components: {TooltipComponent, LoadingComponent},

    setup() {
        const siteStore       = useSiteStore();
        const timezoneStore   = useTimezoneStore();
        const currencyStore   = useCurrencyStore();
        const languageStore   = useLanguageStore();
        const smsGatewayStore = useSmsGatewayStore();
        const storageStore    = useStorageStore();
        const aiAgentStore    = useAiAgentStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            siteStore,
            timezoneStore,
            currencyStore,
            languageStore,
            smsGatewayStore,
            storageStore,
            aiAgentStore,
            frontendSettingStore
        };
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            smsParms: {
                status: activityEnum.ENABLE
            },
            aiAgentParms: {
                status: activityEnum.ENABLE
            },
            storageParms: {
                status: activityEnum.ENABLE
            },
            form: {
                site_date_format: null,
                site_time_format: null,
                site_default_timezone: null,
                site_default_currency: null,
                site_default_ai_agent: null,
                site_default_ai_data_generation_limit: null,
                site_default_currency_symbol: null,
                site_default_language: null,
                site_language_switch: null,
                site_app_debug: null,
                site_auto_update: null,
                site_auto_localization: null,
                site_currency_position: null,
                site_service_fee: null,
                site_email_verification: null,
                site_phone_verification: null,
                site_digit_after_decimal_point: null,
                site_google_map_key: null,
                site_copyright: null,
                site_online_payment_gateway: null,
                site_rider_tip: null,
                site_cutlery: null,
                site_default_sms_gateway: null,
                site_default_storage: null,
                site_restaurant_search_radius: null,
                site_delivery_boy_order_radius: null,
                site_cash_on_delivery: null,
                site_default_order_commission: null,
                site_default_delivery_commission: null,
                site_default_pos_commission: null,
                site_same_time_delivery_boy_maximum_orders_accept_limit: null,
                site_rating_time: null,
                site_return_order_time: null
            },
            enums: {
                dateFormatEnum: dateFormatEnum,
                timeFormatEnum: timeFormatEnum,
                activityEnum: activityEnum,
                currencyPositionEnum: currencyPositionEnum
            },
            errors: {}
        };
    },
    computed: {
        timezones: function () {
            return this.timezoneStore.lists;
        },
        currencies: function () {
            return this.currencyStore.lists;
        },
        languages: function () {
            return this.languageStore.lists;
        },
        smsGateways: function () {
            return this.smsGatewayStore.lists;
        },
        storages: function () {
            return this.storageStore.lists;
        },
        aiAgents: function () {
            return this.aiAgentStore.lists;
        }
    },
    watch: {
        'form.site_default_currency'(id) {
            const selected = (this.currencies || []).find((c) => Number(c.id) === Number(id));
            if (selected?.symbol) {
                this.form.site_default_currency_symbol = selected.symbol;
            }
        }
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.loadProperty();
        } catch (err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    methods: {
        loadProperty: async function () {
            await this.timezoneStore.fetch();
            await this.currencyStore.fetch({
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            });
            await this.aiAgentStore.fetch({
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            });
            await this.languageStore.fetch({
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            });

            await this.siteStore.fetch().then(async (res) => {
                if (res.data.data.site_default_sms_gateway > 0) {
                    this.smsParms['require'] = res.data.data.site_default_sms_gateway;
                }
                await this.smsGatewayStore.fetch(this.smsParms);

                if (res.data.data.site_default_ai_agent > 0) {
                    this.aiAgentParms['require'] = res.data.data.site_default_ai_agent;
                }
                await this.aiAgentStore.fetch(this.aiAgentParms);

                if (res.data.data.site_default_storage > 1) {
                    this.storageParms['require'] = res.data.data.site_default_storage;
                }
                await this.storageStore.fetch(this.storageParms);

                this.form             = {
                    site_date_format: res.data.data.site_date_format,
                    site_time_format: res.data.data.site_time_format,
                    site_default_timezone: res.data.data.site_default_timezone,
                    site_default_currency: res.data.data.site_default_currency,
                    site_default_ai_agent: res.data.data.site_default_ai_agent === 0 ? null : res.data.data.site_default_ai_agent,
                    site_default_ai_data_generation_limit: res.data.data.site_default_ai_data_generation_limit,
                    site_default_currency_symbol: res.data.data.site_default_currency_symbol,
                    site_default_language: res.data.data.site_default_language,
                    site_language_switch: res.data.data.site_language_switch,
                    site_app_debug: res.data.data.site_app_debug,
                    site_auto_update: res.data.data.site_auto_update,
                    site_auto_localization: res.data.data.site_auto_localization,
                    site_currency_position: res.data.data.site_currency_position,
                    site_email_verification: res.data.data.site_email_verification,
                    site_phone_verification: res.data.data.site_phone_verification,
                    site_digit_after_decimal_point: res.data.data.site_digit_after_decimal_point,
                    site_google_map_key: res.data.data.site_google_map_key,
                    site_copyright: res.data.data.site_copyright,
                    site_online_payment_gateway: res.data.data.site_online_payment_gateway,
                    site_rider_tip: res.data.data.site_rider_tip,
                    site_cutlery: res.data.data.site_cutlery,
                    site_default_sms_gateway: res.data.data.site_default_sms_gateway === 0 ? null : res.data.data.site_default_sms_gateway,
                    site_default_storage: res.data.data.site_default_storage,
                    site_restaurant_search_radius: res.data.data.site_restaurant_search_radius,
                    site_delivery_boy_order_radius: res.data.data.site_delivery_boy_order_radius,
                    site_cash_on_delivery: res.data.data.site_cash_on_delivery,
                    site_service_fee: res.data.data.site_service_fee,
                    site_default_order_commission: res.data.data.site_default_order_commission,
                    site_default_delivery_commission: res.data.data.site_default_delivery_commission,
                    site_default_pos_commission: res.data.data.site_default_pos_commission,
                    site_same_time_delivery_boy_maximum_orders_accept_limit: res.data.data.site_same_time_delivery_boy_maximum_orders_accept_limit,
                    site_rating_time: res.data.data.site_rating_time,
                    site_return_order_time: res.data.data.site_return_order_time
                };
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        onlyNumber(e) {
            return appService.onlyNumber(e);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                // Keep form symbol in sync with selected currency before save
                const selected = (this.currencies || []).find(
                    (c) => Number(c.id) === Number(this.form.site_default_currency)
                );
                if (selected?.symbol) {
                    this.form.site_default_currency_symbol = selected.symbol;
                }

                this.siteStore.save(this.form).then(async (res) => {
                    const data = res.data?.data || {};
                    if (data.site_default_currency_symbol) {
                        this.form.site_default_currency_symbol = data.site_default_currency_symbol;
                    }
                    if (data.site_currency_position != null) {
                        this.form.site_currency_position = data.site_currency_position;
                    }
                    if (data.site_digit_after_decimal_point != null) {
                        this.form.site_digit_after_decimal_point = data.site_digit_after_decimal_point;
                    }

                    // Refresh global settings so POS / Waiter / Kitchen / Frontend update immediately
                    try {
                        await this.frontendSettingStore.fetch();
                    } catch (e) {
                        // best-effort; page still works after navigation remount
                    }

                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.site"));
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response?.data?.errors || {};
                    if (!this.errors || Object.keys(this.errors).length === 0) {
                        alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
