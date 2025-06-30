<div id="kt_aside" class="aside aside-default aside-hoverable " data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_toggle">

    <!--begin::Brand-->
    <div class="px-10 pb-5 aside-logo flex-column-auto pt-9" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{ route('admin.dashboard') }}">
            <img alt="Logo" src="{{ asset($systemSetting->logo ?? 'backend/media/logos/logo-default.svg') }}"
                class="max-h-50px logo-default theme-light-show" />
            {{-- <img alt="Logo" src="{{ asset($systemSetting->logo ?? 'backend/media/logos/logo-default.svg') }}"
                class="max-h-50px logo-default theme-dark-show" /> --}}
            <img alt="Logo" src="{{ asset($systemSetting->logo ?? 'backend/media/logos/logo-default.svg') }}"
                class="max-h-50px logo-minimize" />
        </a>
        <!--end::Logo-->
    </div>
    <!--end::Brand-->

    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid ps-3 pe-1">
        <!--begin::Aside Menu-->

        <!--begin::Menu-->
        <div class="my-5 menu menu-sub-indention menu-column menu-rounded menu-title-gray-600 menu-icon-gray-400 menu-active-bg menu-state-primary menu-arrow-gray-500 fw-semibold fs-6 mt-lg-2 mb-lg-0"
            id="kt_aside_menu" data-kt-menu="true">

            <div class="mx-4 hover-scroll-y" id="kt_aside_menu_wrapper" data-kt-scroll="true"
                data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
                data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="20px"
                data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer">

                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-element-11 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                <div class="menu-item">
                    <div class="menu-content">
                        <div class="mx-1 my-2 separator"></div>
                    </div>
                </div>

                {{-- <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"
                        href="{{ route('admin.faqs.index') }}">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24"
                                height="24" stroke-width="2">
                                <path
                                    d="M19.875 6.27c.7 .398 1.13 1.143 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033z">
                                </path>
                                <path d="M12 16v.01"></path>
                                <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                            </svg>
                        </span>
                        <span class="menu-title">FAQ</span>
                    </a>
                </div> --}}

                 <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.service.*') ? 'active' : '' }}"
                        href="{{ route('admin.service.index') }}">
                        <span class="menu-icon">
                            <i class="fa-solid fa-server" style="font-size: 20px;"></i>
                        </span>
                        <span class="menu-title">Services</span>
                    </a>
                </div>
                 <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}"
                        href="{{ route('admin.amenities.index') }}">
                        <span class="menu-icon">
                            <i class="fa-solid fa-snowflake" style="font-size: 20px;"></i>
                        </span>
                        <span class="menu-title">Amenities</span>
                    </a>
                </div>
                 <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.highlights.*') ? 'active' : '' }}"
                        href="{{ route('admin.highlights.index') }}">
                        <span class="menu-icon">
                            <i class="fa-solid fa-clipboard" style="font-size: 20px;"></i>
                        </span>
                        <span class="menu-title">Highlights</span>
                    </a>
                </div>
                 <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.values.*') ? 'active' : '' }}"
                        href="{{ route('admin.values.index') }}">
                        <span class="menu-icon">
                            <i class="fa-solid fa-heart" style="font-size: 20px;"></i>
                        </span>
                        <span class="menu-title">Values</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('subscription-price.*') ? 'active' : '' }}"
                        href="{{ route('subscription-price.edit') }}">
                        <span class="menu-icon">
                            <i class="fa-solid fa-heart" style="font-size: 20px;"></i>
                        </span>
                        <span class="menu-title">Online Store Subcription Price</span>
                    </a>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ request()->routeIs(['profile.setting', 'stripe.setting', 'paypal.setting', 'dynamic_page.*', 'system.index', 'mail.setting', 'social.index']) ? 'active show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fa-solid fa-gear fs-2"></i>
                        </span>
                        <span class="menu-title">Setting</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a href="{{ route('profile.setting') }}"
                                class="menu-link {{ request()->routeIs('profile.setting') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Profile Setting</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('system.index') }}"
                                class="menu-link {{ request()->routeIs('system.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">System Setting</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('dynamic_page.index') }}"
                                class="menu-link {{ request()->routeIs(['dynamic_page.index', 'dynamic_page.create', 'dynamic_page.update']) ? 'active show' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Dynamic Page</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('mail.setting') }}"
                                class="menu-link {{ request()->routeIs('mail.setting') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Mail Setting</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('social.index') }}"
                                class="menu-link {{ request()->routeIs('social.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Social Media</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- cms -->
                <div data-kt-menu-trigger="click"
                    class="menu-item {{ request()->routeIs(['admin.cms.businessHome.banner.index', 'admin.cms.businessHome.stats.index', 'businessHome.grow.index','businessHome.grow.create', 'businessHome.grow.edit','businessHome.grow.sectionTitle.index','businessHome.stayConnection.index','businessHome.stayConnection.create','businessHome.stayConnection.edit','admin.cms.businessHome.getStarted.index','admin.cms.businessHome.interested.index','admin.cms.businessHome.interested.create','admin.cms.businessHome.interested.edit','admin.cms.businessHome.whatOurClientSay.index','client_review.index','client_review.edit','client_review.create','businessPricing.banner.index','businessPricing.sectionTitle.index','businessPricing.sectionDescription.index','businessPricing.description.index','businessPricing.description.create','businessPricing.description.edit','businessPricing.faq.index','businessPricing.faq.create','businessPricing.faq.edit','admin.cms.businessHelp.banner.index','admin.cms.businessHelp.popularArticleBanner.index','businessHelp.popularArticles.index','businessHelp.popularArticles.create','businessHelp.popularArticles.edit','businessHelp.knowledgeBaseBanner.index','businessHelp.knowledgeBase.index','businessHelp.knowledgeBase.create','businessHelp.knowledgeBase.edit','businessHelp.help.index','blog.index','blog.create','blog.edit','blogCategory.index','blogCategory.create','blogCategory.edit','cms.blog.banner.index','cms.blog.footer.index']) ? 'active show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fa-solid fa-sliders fs-2"></i>
                        </span>
                        <span class="menu-title">CMS</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a href="{{ route('admin.cms.businessHome.banner.index') }}"
                                class="menu-link {{ request()->routeIs('admin.cms.businessHome.banner.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Banner</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('admin.cms.businessHome.stats.index') }}"
                                class="menu-link {{ request()->routeIs('admin.cms.businessHome.stats.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Stats</span>
                            </a>
                        </div>


                        <div class="menu-item">
                            <a href="{{ route('businessHome.grow.sectionTitle.index') }}"
                                class="menu-link {{ request()->routeIs('businessHome.grow.sectionTitle.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Grow Title</span>
                            </a>
                        </div>



                        <div class="menu-item">
                            <a href="{{ route('businessHome.grow.index') }}"
                                class="menu-link {{ request()->routeIs('businessHome.grow.index','businessHome.grow.create','businessHome.grow.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Grow Cards</span>
                            </a>
                        </div>


                        <div class="menu-item">
                            <a href="{{ route('businessHome.stayConnection.index') }}"
                                class="menu-link {{ request()->routeIs('businessHome.stayConnection.index','businessHome.stayConnection.create','businessHome.stayConnection.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Stay Connection</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('admin.cms.businessHome.getStarted.index') }}"
                                class="menu-link {{ request()->routeIs('admin.cms.businessHome.getStarted.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Get Started Banner</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('admin.cms.businessHome.interested.index') }}"
                                class="menu-link {{ request()->routeIs('admin.cms.businessHome.interested.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Interested Section</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('admin.cms.businessHome.whatOurClientSay.index') }}"
                                class="menu-link {{ request()->routeIs('admin.cms.businessHome.whatOurClientSay.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home What Our Client Say Banner</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('client_review.index') }}"
                                class="menu-link {{ request()->routeIs('client_review.index','client_review.edit','client_review.create') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Home Client Review</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('businessPricing.banner.index') }}"
                                class="menu-link {{ request()->routeIs('businessPricing.banner.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Pricing Banner</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('businessPricing.sectionTitle.index') }}"
                                class="menu-link {{ request()->routeIs('businessPricing.sectionTitle.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Pricing Section Title</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('businessPricing.sectionDescription.index') }}"
                                class="menu-link {{ request()->routeIs('businessPricing.sectionDescription.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Pricing Section Description</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('businessPricing.description.index') }}"
                                class="menu-link {{ request()->routeIs('businessPricing.description.index','businessPricing.description.create','businessPricing.description.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Pricing Lists</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('businessPricing.faq.index') }}"
                                class="menu-link {{ request()->routeIs('businessPricing.faq.index','businessPricing.faq.create','businessPricing.faq.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Pricing FAQs</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('admin.cms.businessHelp.banner.index') }}"
                                class="menu-link {{ request()->routeIs('admin.cms.businessHelp.banner.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Help Banner</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('admin.cms.businessHelp.popularArticleBanner.index') }}"
                                class="menu-link {{ request()->routeIs('admin.cms.businessHelp.popularArticleBanner.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Help Popular Article Banner</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('businessHelp.popularArticles.index') }}"
                                class="menu-link {{ request()->routeIs('businessHelp.popularArticles.index','businessHelp.popularArticles.create','businessHelp.popularArticles.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Help Popular Articles</span>
                            </a>
                        </div>

                        <!-- knowledge base banner -->
                        <div class="menu-item">
                            <a href="{{ route('businessHelp.knowledgeBaseBanner.index') }}"
                                class="menu-link {{ request()->routeIs('businessHelp.knowledgeBaseBanner.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Help Knowledge Base Banner</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a href="{{ route('businessHelp.knowledgeBase.index') }}"
                                class="menu-link {{ request()->routeIs('businessHelp.knowledgeBase.index','businessHelp.knowledgeBase.create','businessHelp.knowledgeBase.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Help Knowledge Base</span>
                            </a>
                        </div>
                        <!-- business help page help section -->
                        <div class="menu-item">
                            <a href="{{ route('businessHelp.help.index') }}"
                                class="menu-link {{ request()->routeIs('businessHelp.help.index','businessHelp.help.create','businessHelp.help.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Business Help Page Help Section</span>
                            </a>
                        </div>
                        <!-- blog banner -->
                        <div class="menu-item">
                            <a href="{{ route('cms.blog.banner.index') }}"
                                class="menu-link {{ request()->routeIs('cms.blog.banner.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Blog Banner</span>
                            </a>
                        </div>

                        <!-- blog category -->
                        <div class="menu-item">
                            <a href="{{ route('blogCategory.index') }}"
                                class="menu-link {{ request()->routeIs('blogCategory.index','blogCategory.create','blogCategory.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Blog Category</span>
                            </a>
                        </div>
                        <!-- blogs -->
                        <div class="menu-item">
                            <a href="{{ route('blog.index') }}"
                                class="menu-link {{ request()->routeIs('blog.index','blog.create','blog.edit') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Blogs</span>
                            </a>
                        </div>
                        {{-- blog footer --}}
                        <div class="menu-item">
                            <a href="{{ route('cms.blog.footer.index') }}"
                                class="menu-link {{ request()->routeIs('cms.blog.footer.index') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Blog Footer</span>
                            </a>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
