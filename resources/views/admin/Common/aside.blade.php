<aside class="wrapping-aside" id="wrappingAside">

    <div class="aside-content">
        <div>
            <a href="{{route('admin.dashboard')}}" class="aside-nav-link {{@$menu == 'dashboard' ? 'active' : ''}}">
                <span class="aside-nav-icon"><i class="icon-home"></i></span>
                <span class="aside-nav-text shrink-text-toggleable">Dashboard</span>
            </a>
        </div>


        <ul class="list-unstyled aside-nav-list toggleable-group aside-nav-list-slim">

            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-pie-chart"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Product</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'category' || @$menu == 'unit' || @$menu == 'tax' || @$menu == 'products' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="{{route('category.index')}}" class="aside-nav-sublink {{@$menu == 'category' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Category</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('unit.index')}}" class="aside-nav-sublink {{@$menu == 'unit' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Unit</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('tax.index')}}" class="aside-nav-sublink {{@$menu == 'tax' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Tax</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('admin.products.index')}}" class="aside-nav-sublink {{@$menu == 'products' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Products</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-product-hunt"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Purchase</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">New Purchase</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">All Purchase</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>



            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Sales</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">New Sale</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">All Sales</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Expense</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Category</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Expense </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Payment</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Payment to Supplier</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Received from Customer </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-line-chart"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Sales Reports</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Sales Summary</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Product Wise </span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">All Sales </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-line-chart"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Purchases Reports</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Purchase Summary</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Product Wise </span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">All Purchases </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="aside-nav-item">
                <a href="{{route('store.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'store' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-shopping-bag"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Store</span>
                </a>
            </li>

            <li class="aside-nav-item">
                <a href="{{route('staff.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'staff' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-users"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Staffs</span>
                </a>
            </li>

            <li class="aside-nav-item">
                <a href="{{route('suppliers.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'suppliers' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-user-secret"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Suppliers</span>
                </a>
            </li>
            <li class="aside-nav-item">
                <a href="{{route('customers.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'customers' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-users"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Customers</span>
                </a>
            </li>


            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-cog"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Application Settings</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'settings' || @$menu == 'currency' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="{{route('admin.settings.index')}}" class="aside-nav-sublink {{@$menu == 'settings' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">General Settings</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('admin.currency.index')}}" class="aside-nav-sublink {{@$menu == 'currency' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Currency</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="#" class="aside-nav-sublink">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Roles & Permissions</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


        </ul>

    </div>
</aside>
