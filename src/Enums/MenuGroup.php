<?php

declare(strict_types=1);

namespace Rimba\Menu\Enums;

enum MenuGroup: string
{
    // Enterprise
    case StrategyManagement = 'strategy-management';
    case GovernanceManagement = 'governance-management';
    case FinancialManagement = 'financial-management';
    case PerformanceManagement = 'performance-management';
    case LegalManagement = 'legal-management';

    // People
    case WorkforcePlanning = 'workforce-planning';
    case TalentAcquisition = 'talent-acquisition';
    case EmployeeManagement = 'employee-management';
    case PerformanceDevelopment = 'performance-development';
    case LearningManagement = 'learning-management';

    // Market
    case CustomerManagement = 'customer-management';
    case ProductManagement = 'product-management';
    case ServiceManagement = 'service-management';
    case SalesManagement = 'sales-management';
    case PartnerManagement = 'partner-management';

    // Supply
    case SupplierManagement = 'supplier-management';
    case ProcurementManagement = 'procurement-management';
    case InventoryManagement = 'inventory-management';
    case WarehouseManagement = 'warehouse-management';
    case LogisticsManagement = 'logistics-management';

    // Operate
    case OperationsPlanning = 'operations-planning';
    case ProductionManagement = 'production-management';
    case QualityManagement = 'quality-management';
    case MaintenanceManagement = 'maintenance-management';
    case SafetyManagement = 'safety-management';
    case ProcessManagement = 'process-management';

    // Technology
    case ApplicationManagement = 'application-management';
    case InformationManagement = 'information-management';
    case ITServiceManagement = 'it-service-management';
    case SecurityManagement = 'security-management';
    case DigitalInnovation = 'digital-innovation';

    // Knowledge
    case DocumentManagement = 'document-management';
    case KnowledgeBase = 'knowledge-base';
    case CorporateCommunication = 'corporate-communication';
    case Standards = 'standards';
    case Reports = 'reports';
    case AIKnowledge = 'ai-knowledge';

    // Source
    case Organization = 'organization';
    case People = 'people';
    case ProductsServices = 'products-services';
    case Resources = 'resources';
    case Locations = 'locations';
    case Business = 'business';
    case Community = 'community';

    public function label(): string
    {
        return str($this->value)
            ->replace('-', ' ')
            ->title()
            ->value();
    }

    public function category(): MenuCategory
    {
        return match ($this) {

            self::StrategyManagement,
            self::GovernanceManagement,
            self::FinancialManagement,
            self::PerformanceManagement,
            self::LegalManagement => MenuCategory::Enterprise,

            self::WorkforcePlanning,
            self::TalentAcquisition,
            self::EmployeeManagement,
            self::PerformanceDevelopment,
            self::LearningManagement => MenuCategory::People,

            self::CustomerManagement,
            self::ProductManagement,
            self::ServiceManagement,
            self::SalesManagement,
            self::PartnerManagement => MenuCategory::Market,

            self::SupplierManagement,
            self::ProcurementManagement,
            self::InventoryManagement,
            self::WarehouseManagement,
            self::LogisticsManagement => MenuCategory::Supply,

            self::OperationsPlanning,
            self::ProductionManagement,
            self::QualityManagement,
            self::MaintenanceManagement,
            self::SafetyManagement,
            self::ProcessManagement => MenuCategory::Operate,

            self::ApplicationManagement,
            self::InformationManagement,
            self::ITServiceManagement,
            self::SecurityManagement,
            self::DigitalInnovation => MenuCategory::Technology,

            self::DocumentManagement,
            self::KnowledgeBase,
            self::CorporateCommunication,
            self::Standards,
            self::Reports,
            self::AIKnowledge => MenuCategory::Knowledge,

            self::Organization,
            self::People,
            self::ProductsServices,
            self::Resources,
            self::Locations,
            self::Business,
            self::Community => MenuCategory::Source,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [
                $case->value => $case->label(),
            ])
            ->all();
    }

    public static function optionsForCategory(
        MenuCategory $category
    ): array {
        return collect(self::cases())
            ->filter(
                fn (self $group): bool => $group->category() === $category
            )
            ->mapWithKeys(
                fn (self $group): array => [
                    $group->value => $group->label(),
                ]
            )
            ->all();
    }
}
