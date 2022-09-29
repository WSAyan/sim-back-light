<?php

use App\Account;
use App\Brand;
use App\Category;
use App\DeliveryMethod;
use App\OrderStatus;
use App\PaymentMethod;
use App\PaymentStatus;
use App\ProductOption;
use App\ProductOptionsDetail;
use App\Role;
use App\RoleVUser;
use App\Screen;
use App\ScreenType;
use App\Tax;
use App\User;
use App\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

define("MAIN_ACCOUNT", "SIM000000000000000");

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->insertRoles();

        $this->insertUsers();

        $this->insertAccount();

        $this->insertCategories();

        $this->insertBrands();

        $this->insertDeliveryMethods();

        $this->insertOrderStatus();

        $this->insertPaymentMethods();

        $this->insertPaymentStatus();

        $this->insertTax();

        $this->insertUnits();

        $this->insertProductOptions();

        $this->insertProductOptionsDetails();

        $this->insertScreenTypes();

        $this->insertScreens();
    }

    private function insertRoles()
    {
        $role_super_admin = Role::create([
            'rolename' => 'super admin'
        ]);

        $role_admin = Role::create([
            'rolename' => 'admin'
        ]);

        $role_manager = Role::create([
            'rolename' => 'manager'
        ]);

        $role_sales_person = Role::create([
            'rolename' => 'sales person'
        ]);

        $role_delivery_person = Role::create([
            'rolename' => 'delivery person'
        ]);

        $role_customer = Role::create([
            'rolename' => 'collector'
        ]);

        $role_visitor = Role::create([
            'rolename' => 'retailer'
        ]);
    }

    private function insertUsers()
    {
        $super_admin_user = User::create([
            'username' => 'Saitama',
            'email' => 'saitama',
            'password' => Hash::make('(Snc9^.23m4FC)'),
        ]);

        $super_admin_user_role = RoleVUser::create([
            'role_id' => '1',
            'user_id' => '1',
        ]);

        $admin_user = User::create([
            'username' => 'Admin',
            'email' => 'admin',
            'password' => Hash::make('y1z(*!@<M:S?'),
        ]);

        $admin_user_role = RoleVUser::create([
            'role_id' => '2',
            'user_id' => '2',
        ]);

        $manager_user = User::create([
            'username' => 'Pep Guardiola',
            'email' => 'pep@pep.sim',
            'password' => Hash::make('man#1sim'),
        ]);

        $manager_user_role = RoleVUser::create([
            'role_id' => '3',
            'user_id' => '3',
        ]);

        $sales_user = User::create([
            'username' => 'Saul Goodman',
            'email' => 'saul@saul.sim',
            'password' => Hash::make('123456'),
        ]);

        $sales_user_role = RoleVUser::create([
            'role_id' => '4',
            'user_id' => '4',
        ]);

        $delivery_user = User::create([
            'username' => 'Bary Allen',
            'email' => 'flash@flash.sim',
            'password' => Hash::make('123456'),
        ]);

        $delivery_user_role = RoleVUser::create([
            'role_id' => '5',
            'user_id' => '5',
        ]);

        $customer_user = User::create([
            'username' => 'Test Collector 1',
            'email' => '01712012345',
            'password' => Hash::make('123456'),
        ]);

        $customer_user_role = RoleVUser::create([
            'role_id' => '6',
            'user_id' => '6',
        ]);

        $visitor_user = User::create([
            'username' => 'Test Retailer 1',
            'email' => '01712420420',
            'password' => Hash::make('123456'),
        ]);

        $visitor_user_role = RoleVUser::create([
            'role_id' => '7',
            'user_id' => '7',
        ]);
    }

    private function insertCategories()
    {
        $categories_example_1 = Category::create([
            'name' => 'Household',
            'description' => 'House hold related items'
        ]);

        $categories_example_2 = Category::create([
            'name' => 'Foods and Beverages',
            'description' => 'Foods and beverages related items'
        ]);
    }

    private function insertAccount()
    {
        $main_acc = Account::create([
            'user_id' => 1,
            'account_no' => MAIN_ACCOUNT,
            'balance' => 0.0,
        ]);
    }

    private function insertBrands()
    {
        $brands_example_1 = Brand::create([
            'brand_name' => 'shadharon brand'
        ]);

        $brands_example_2 = Brand::create([
            'brand_name' => 'oshadahron brand'
        ]);
    }

    private function insertDeliveryMethods()
    {
        $delivery_methods_not_needed = DeliveryMethod::create([
            'delivery_method' => 'Not needed/Counter sale'
        ]);

        $delivery_methods_own_delivery = DeliveryMethod::create([
            'delivery_method' => 'Own delivery'
        ]);

        $delivery_methods_courier = DeliveryMethod::create([
            'delivery_method' => 'X courier service'
        ]);
    }

    private function insertOrderStatus()
    {
        $orders_created = OrderStatus::create([
            'status' => 'Created'
        ]);

        $orders_processing = OrderStatus::create([
            'status' => 'Processing'
        ]);

        $orders_on_the_way = OrderStatus::create([
            'status' => 'On the way'
        ]);

        $orders_delivered = OrderStatus::create([
            'status' => 'Delivered'
        ]);

        $orders_completed = OrderStatus::create([
            'status' => 'Completed'
        ]);

        $orders_cancelled = OrderStatus::create([
            'status' => 'Cancelled'
        ]);
    }

    private function insertPaymentMethods()
    {
        $payment_methods_cash = PaymentMethod::create([
            'payment_method' => 'Cash'
        ]);

        $payment_methods_mobile_banking = PaymentMethod::create([
            'payment_method' => 'Mobile banking name'
        ]);

        $payment_methods_card = PaymentMethod::create([
            'payment_method' => 'Card payment'
        ]);
    }

    private function insertPaymentStatus()
    {
        $payment_status_paid = PaymentStatus::create([
            'status' => 'Paid'
        ]);

        $payment_status_due = PaymentStatus::create([
            'status' => 'Due'
        ]);
    }

    private function insertTax()
    {
        $tax_zero = Tax::create([
            'tax_method' => 'zero',
            'percentage' => '0.0',
            'tax_invoice_number' => null
        ]);
    }

    private function insertUnits()
    {
        $units_count = Unit::create([
            'unit_name' => 'count',
            'is_reminder_allowed' => false,
        ]);

        $units_weight = Unit::create([
            'unit_name' => 'kg',
            'is_reminder_allowed' => true,
        ]);
    }

    private function insertProductOptions()
    {
        $size_option = ProductOption::create([
            'name' => 'Sizes'
        ]);

        $color_option = ProductOption::create([
            'name' => 'Colors'
        ]);
    }

    private function insertProductOptionsDetails()
    {
        $size_option_s = ProductOptionsDetail::create([
            'product_options_id' => 1,
            'name' => 'S'
        ]);

        $size_option_l = ProductOptionsDetail::create([
            'product_options_id' => 1,
            'name' => 'L'
        ]);

        $size_option_xl = ProductOptionsDetail::create([
            'product_options_id' => 1,
            'name' => 'XL'
        ]);

        $size_option_xxl = ProductOptionsDetail::create([
            'product_options_id' => 1,
            'name' => 'XXL'
        ]);

        $color_option_red = ProductOptionsDetail::create([
            'product_options_id' => 2,
            'name' => 'Red'
        ]);

        $color_option_blue = ProductOptionsDetail::create([
            'product_options_id' => 2,
            'name' => 'Blue'
        ]);

        $color_option_yellow = ProductOptionsDetail::create([
            'product_options_id' => 2,
            'name' => 'Yellow'
        ]);
    }

    private function insertScreenTypes()
    {
        $row = ScreenType::create([
            'screen_type' => 'drawer_menu'
        ]);

        $row = ScreenType::create([
            'screen_type' => 'sub_menu'
        ]);

        $row = ScreenType::create([
            'screen_type' => 'common'
        ]);

        $row = ScreenType::create([
            'screen_type' => 'toolbar_menu'
        ]);

        $row = ScreenType::create([
            'screen_type' => 'bottom_menu'
        ]);
    }

    private function insertScreens()
    {
        $row = Screen::create([
            'screen_name' => 'Dashboard',
            'route' => '/user/dashboard',
            'icon' => 'view-dashboard'
        ]);

        $row = Screen::create([
            'screen_name' => 'Users',
            'route' => '/users',
            'icon' => 'account-group'
        ]);

        $row = Screen::create([
            'screen_name' => 'Orders',
            'route' => '/order/orders',
            'icon' => 'cart'
        ]);

        $row = Screen::create([
            'screen_name' => 'Brands',
            'route' => '/brand/brands',
            'icon' => 'palette-swatch'
        ]);

        $row = Screen::create([
            'screen_name' => 'Categories',
            'route' => '/category/categories',
            'icon' => 'label'
        ]);

        $row = Screen::create([
            'screen_name' => 'Products',
            'route' => '/product/products',
            'icon' => 'semantic-web'
        ]);

        $row = Screen::create([
            'screen_name' => 'Reports',
            'route' => '/report/reports',
            'icon' => 'chart-box'
        ]);

        $row = Screen::create([
            'screen_name' => 'Settings',
            'route' => '/settings',
            'icon' => 'cog'
        ]);
    }
}
