<aside class="wrapping-aside" id="wrappingAside">

    <div class="aside-content">
        @can('view_dashboard')
        <div>
            <a href="{{route('admin.dashboard')}}" class="aside-nav-link {{@$menu == 'dashboard' ? 'active' : ''}}">
                <span class="aside-nav-icon"><i class="icon-home"></i></span>
                <span class="aside-nav-text shrink-text-toggleable">Dashboard</span>
            </a>
        </div>
        @endcan

        <ul class="list-unstyled aside-nav-list toggleable-group aside-nav-list-slim">

            @canany(['manage_category', 'manage_unit', 'manage_tax', 'manage_vat', 'manage_product'])
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-pie-chart"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Product</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'category' || @$menu == 'unit' || @$menu == 'tax' || @$menu == 'vat' || @$menu == 'products' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        @can('manage_category')
                        <li class="aside-nav-item">
                            <a href="{{route('category.index')}}" class="aside-nav-sublink {{@$menu == 'category' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Category</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage_unit')
                        <li class="aside-nav-item">
                            <a href="{{route('unit.index')}}" class="aside-nav-sublink {{@$menu == 'unit' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Unit</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage_vat')
                        <li class="aside-nav-item">
                            <a href="{{route('vat.index')}}" class="aside-nav-sublink {{@$menu == 'vat' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">VAT</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage_tax')
                        <li class="aside-nav-item">
                            <a href="{{route('tax.index')}}" class="aside-nav-sublink {{@$menu == 'tax' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Tax</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage_product')
                        <li class="aside-nav-item">
                            <a href="{{route('admin.products.index')}}" class="aside-nav-sublink {{@$menu == 'products' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Products</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @can('manage_purchase')
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-product-hunt"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Purchase</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'purchase' || @$menu == 'create-purchase' ? 'show' : ''}} ">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="{{route('purchase.create')}}" class="aside-nav-sublink {{@$menu == 'create-purchase' ? 'active' : '' }}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">New Purchase</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('purchase.index')}}" class="aside-nav-sublink {{@$menu == 'purchase' ? 'active' : '' }}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">All Purchase</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @can('manage_sale')
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Sales</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'invoice' || @$menu == 'create-invoice' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="{{route('invoice.create')}}" class="aside-nav-sublink {{@$menu == 'create-invoice' ? 'active' : '' }}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">New Sale</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('invoice.index')}}" class="aside-nav-sublink {{@$menu == 'invoice' ? 'active' : '' }}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">All Sales</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @can('manage_income')
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Income</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'income-category' || @$menu == 'income' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="{{route('income-category.index')}}" class="aside-nav-sublink {{@$menu == 'income-category' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Category</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('incomes.index')}}" class="aside-nav-sublink {{@$menu == 'income' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Income</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @can('manage_expense')
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Expense</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'expense-category' || @$menu == 'expense' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="{{route('expense-category.index')}}" class="aside-nav-sublink {{@$menu == 'expense-category' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Category</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('expenses.index')}}" class="aside-nav-sublink {{@$menu == 'expense' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Expense</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @can('manage_payment')
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Payment</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'payment-to-supplier' || @$menu == 'payment-from-customer' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        <li class="aside-nav-item">
                            <a href="{{route('payment-to-supplier.index')}}" class="aside-nav-sublink {{@$menu == 'payment-to-supplier' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Payment to Supplier</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('payment-from-customer.index')}}" class="aside-nav-sublink {{@$menu == 'payment-from-customer' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Received from Customer</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @canany(['manage_asset', 'manage_debt', 'manage_lend', 'manage_security_money'])
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-dollar"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Finance</span>
                </a>
                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'asset' || @$menu == 'debt' || @$menu == 'lend' || @$menu == 'security-money' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        @can('manage_asset')
                        <li class="aside-nav-item">
                            <a href="{{route('admin.asset.index')}}" class="aside-nav-sublink {{@$menu == 'asset' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Assets</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage_debt')
                        <li class="aside-nav-item">
                            <a href="{{route('admin.debt.index')}}" class="aside-nav-sublink {{@$menu == 'debt' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Debts</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage_lend')
                        <li class="aside-nav-item">
                            <a href="{{route('admin.lend.index')}}" class="aside-nav-sublink {{@$menu == 'lend' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Lend</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage_security_money')
                        <li class="aside-nav-item">
                            <a href="{{route('admin.security-money.index')}}" class="aside-nav-sublink {{@$menu == 'security-money' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Security Money</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['view_sale_reports', 'view_revenue_reports'])
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-line-chart"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Sales Reports</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'sales-report-summary' || @$menu == 'sales-report-product-wise' || @$menu == 'sales-report-revenue' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        @can('view_sale_reports')
                        <li class="aside-nav-item">
                            <a href="{{route('sales-report.summary')}}" class="aside-nav-sublink {{@$menu == 'sales-report-summary' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Sales Summary</span>
                            </a>
                        </li>
                        <li class="aside-nav-item">
                            <a href="{{route('sales-report.product-wise')}}" class="aside-nav-sublink {{@$menu == 'sales-report-product-wise' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Product Wise</span>
                            </a>
                        </li>
                        @endcan
                        @can('view_revenue_reports')
                        <li class="aside-nav-item">
                            <a href="{{route('sales-report.revenue')}}" class="aside-nav-sublink {{@$menu == 'sales-report-revenue' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Revenue Report</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['view_purchase_reports', 'view_stock_reports'])
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-line-chart"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Stock Reports</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'purchase-report' || @$menu == 'stock-report' ? 'show' : ''}}">
                    <ul class="list-unstyled aside-nav-list">
                        @can('view_purchase_reports')
                        <li class="aside-nav-item">
                            <a href="{{route('purchase-report.index')}}" class="aside-nav-sublink {{@$menu == 'purchase-report' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Purchase Reports</span>
                            </a>
                        </li>
                        @endcan
                        @can('view_stock_reports')
                        <li class="aside-nav-item">
                            <a href="{{route('stock-report.index')}}" class="aside-nav-sublink {{@$menu == 'stock-report' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Stock Reports</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @can('admin_permission')
            <li class="aside-nav-item">
                <a href="{{route('store.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'store' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-shopping-bag"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Store</span>
                </a>
            </li>
            @endcan

            @can('manage_staff')
            <li class="aside-nav-item">
                <a href="{{route('staff.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'staff' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-users"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Staffs</span>
                </a>
            </li>
            @endcan

            @can('manage_supplier')
            <li class="aside-nav-item">
                <a href="{{route('suppliers.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'suppliers' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-user-secret"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Suppliers</span>
                </a>
            </li>
            @endcan

            @can('admin_permission')
            <li class="aside-nav-item toggle-item">
                <a href="#" class="aside-nav-link toggler toggle-icon">
                    <span class="aside-nav-icon"><i class="fa fa-cog"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Application Settings</span>
                </a>

                <div class="aside-nav-dropdown toggleable-content {{@$menu == 'settings' || @$menu == 'currency' || @$menu == 'roles' ? 'show' : ''}}">
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
                            <a href="{{route('admin.roles.index')}}" class="aside-nav-sublink {{@$menu == 'roles' ? 'active' : ''}}">
                                <span class="aside-nav-icon"><i class="fa fa-circle-o"></i></span>
                                <span class="aside-nav-text shrink-text-toggleable">Roles & Permissions</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @can('manage_customer')
            <li class="aside-nav-item">
                <a href="{{route('customers.index')}}" class="aside-nav-link aside-nav-link-small {{@$menu == 'customers' ? 'active' : ''}}">
                    <span class="aside-nav-icon"><i class="fa fa-users"></i></span>
                    <span class="aside-nav-text shrink-text-toggleable">Manage Customers</span>
                </a>
            </li>
            @endcan

        </ul>

    </div>
</aside>
