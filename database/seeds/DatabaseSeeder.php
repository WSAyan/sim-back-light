<?php

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
use App\Tax;
use App\User;
use App\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'rolename' => 'customer'
        ]);

        $role_visitor = Role::create([
            'rolename' => 'visitor'
        ]);
    }

    private function insertUsers()
    {
        $super_admin_user = User::create([
            'username' => 'Saitama',
            'email' => 'saitama@saitama.sim',
            'password' => Hash::make('one_punch_man_123456'),
        ]);

        $super_admin_user_role = RoleVUser::create([
            'role_id' => '1',
            'user_id' => '1',
        ]);

        $admin_user = User::create([
            'username' => 'Mob Psycho',
            'email' => 'mob@mob.sim',
            'password' => Hash::make('123456'),
        ]);

        $admin_user_role = RoleVUser::create([
            'role_id' => '2',
            'user_id' => '2',
        ]);

        $manager_user = User::create([
            'username' => 'Pep Guardiola',
            'email' => 'pep@pep.sim',
            'password' => Hash::make('123456'),
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
            'username' => 'Two Chainz',
            'email' => '2chainz@2chainz.sim',
            'password' => Hash::make('123456'),
        ]);

        $customer_user_role = RoleVUser::create([
            'role_id' => '6',
            'user_id' => '6',
        ]);

        $visitor_user = User::create([
            'username' => 'Ibne Batuta',
            'email' => 'ibnebatuta@ibne.sim',
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
}
