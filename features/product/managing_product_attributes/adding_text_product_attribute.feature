@managing_product_attributes
Feature: Adding a new text product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new text product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new text product attribute
        When I want to create a new text product attribute
        And I specify its code as "t_shirt_brand"
        And I name it "T-Shirt brand" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the text attribute "T-Shirt brand" should appear in the store

    @ui
    Scenario: Seeing disabled type field while adding text a product attribute
        When I want to create a new text product attribute
        Then the type field should be disabled
