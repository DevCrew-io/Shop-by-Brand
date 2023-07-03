# DevCrew Shop By Brand

[Shop By Brand]() allows you to sell your products by "Brand" and provide a new level of looking up products for your shop visitors. You can create brands of your choice and manage those in your shop by creating individual and unique brand pages with brand names, logos, specific URLs, banner images, descriptions, SEO fields, and related products to showcase brand products separately.

This extension provides a custom brand page listing all the available brands with logos and product counts. The customers can search among the brands listed and buy their desired item. You can manage that page with the help of configurations.

[Shop By Brand]() extension allows you to display the brand name and logo on the product detail page.

## Key Features

- Add and manage brands easily by a separate menu from the Admin grid.
- Assign and remove products to/from specific brands easily by a brand form page.
- Add SEO-friendly URLs for any individual brand page.
- By default, brands are displayed on the homepage, but you can add that block on any CMS page by simply placing the block code (explained in the below section).
- Provides a customizable brand listing page where all searchable brands are shown.
- Allows displaying the brand name and logo on product detail pages.

## How to install?

You can purchase this extension from the Magento marketplace for Free of Cost. After successfully placing the order, you can install this extension using Composer. The installation guide is also available in the "My Purchases" section.

## How to Configure?

From the Admin Panel, navigate to `Stores > Settings > Configuration`. From the left navigation, expand the **DevCrew** tab select the **Shop By Brand** extension.

![config-main](https://i.imgur.com/fZfZUWK.png)

### General Configuration

In the **General Configuration** section, you can enable this extension, add the title used for the brands' page and check the URL for the brand listing page (which can be used in the menu bar).

![config-general](https://i.imgur.com/IB6PNMX.png)

- **Enable Shop By Brand**: Select **Yes** to use the module's features.
- **Title**: Title of the extension.
- **Brands Router Name**: Brands listing page URL.

### Brand List Page Setting

In the **Brand List Page Setting** section, you can customize the brand listing page for showing/hiding products count against each brand can be managed in this configuration.

![config-brand-list](https://i.imgur.com/UPSUn6f.png)

- **Show Product Count**: Select **Yes** to display the total products count next to the Brand’s name.

### Product Detail Page Setting

In the **Product Detail Page Setting** section, you can show/hide the brand logo and name on the product detail page.

![config-general](https://i.imgur.com/yn54TAA.png)

- **Show Logo on Product Page**: Select **Yes** to display the brand’s logo and name on the product detail page.

### Brands Block

In the Brands Block section, you can show/hide the brand’s block on the homepage. This feature is available in the FREE version. You can hide that block using the following configurations.

![config-brands-block](https://i.imgur.com/VNiB9So.png)

### Brand Pro (Paid Version)

The grayed-out configurations, as in the below screenshot, are a part of the Brands Pro module. You can purchase the PAID version of the extension from our website: [devcrew.io](devcrew.io), to access all the PRO features.

![brand-pro-config](https://i.imgur.com/UMYfl36.png)

## How to Add/Manage Brands

![brands-menu](https://i.imgur.com/NnjmqEA.png)

To add and manage brands from the Admin Panel, navigate to `DEVCREW > Manage Brands `. A grid will be shown where all the brands will be listed after addition.

![brands-grid](https://i.imgur.com/z1Ehwzw.png)

### Filters

Using the [Shop By Brand]() extension, now Admin can search any brand with the help of different filters like ID, Brand Title, URL Key, Status of Brand, Position, and Brand creation/updation date as shown in the above screenshot.

The Action dropdown has multiple actions by which the admin user can delete, enable or disable the brands.

### Add New Brand

![brands-form-1](https://i.imgur.com/y2SxrJF.png)

After clicking on the "Add New Brand" button from the brand grid, you can see a brand form that contains two tabs. The first tab contains form fields for brand information, and using the second tab, you can assign products to that brand.

- **Status**: For setting a brand’s status. If any brand is set to **Disable**, it will not be visible on the storefront.
- **Title**: Brand’s title.
- **Description**: You can add a description to this field to display on the brand page.
- **URL Key**: You can add a unique URL to a brand.
- **Store Views**: You can select single or multiple stores for a brand.
- **Brand Logo**: Logo of the brand.
- **Banner Image**: To set a banner image that will be visible on the brand view page.
- **Position**: To set the position of the brand.
- **SEO Meta Title**: Meta title of the brand page.
- **SEO Meta Keywords**: Meta keywords of the brand page.
- **SEO Meta Description**: Meta description of the brand page.

![brands-form-2](https://i.imgur.com/ScbrkVH.png)

After adding brand information, you can assign products to the brand using the second tab of the form "Assign Products." Here product grid will be shown from where you can assign/un-assign products using ID, Name, SKU, Product visibility, Status, and Price filters.

After assigning the product click the "Save Brand" button to save the brand and redirect to the brands' grid.

### Brands Tab Block

![brand-tab](https://i.imgur.com/GVugue8.png)

You can add the brands block on any CMS page from admin panel by adding the following code in that CMS page "Content" section as shown above.

``
{{block class="Devcrew\Brand\Block\BrandTab" name="devcrew.brands.tab" template="Devcrew_Brand::block/brands.phtml"}}
``

## Brands on Storefront

### Brands on Home Page

The brands will display on home page like showing in the below image.

![home-page-brands](https://i.imgur.com/fQNbpvV.png)

After clicking "View All," you will be redirected to the brands listing page where all brands will be listed.

### Brands Listing Page

![brands-listing](https://i.imgur.com/kNEQCur.png)

Brand Listing is a page where all active brands are shown, and the customer can search for whatever brand he wants to buy products from.

### Brand View Page

![brand-view](https://i.imgur.com/cwfM1gC.png)

The brand view pages are the individual pages that contain brand information and product listings similar to other category pages in the Magento shop.

### Brand on Product Detail Page

As products are assigned to a specific brand, it is necessary to show brand information on the product detail page. Below is the image that shows how brand information will be shown on the product detail page using our extension [Shop By Brand]().

![product-view-page](https://i.imgur.com/fclE5Y0.png)

**Note:** You can disable this feature using the configurations explained above.

## Feature Request and Bug Report

If you want to include any feature or find any bugs, let us know at [hello@devcrew.io](mailto:hello@devcrew.io) 

## Our Shop By Brand (Pro) Extension
You can purchase our Pro version here: [Shop By Brand (Pro)](https://devcrew.io/product/magento-shop-by-brand-extension/)
