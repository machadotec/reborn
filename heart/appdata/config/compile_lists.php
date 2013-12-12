<?php

return array_map('realpath', array(
	SYSTEM.'vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/Request.php',
	SYSTEM.'vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/Response.php',
	SYSTEM.'vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/RedirectResponse.php',
	SYSTEM.'vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/JsonResponse.php',
	SYSTEM.'vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/StreamedResponse.php',
	SYSTEM.'vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/BinaryFileResponse.php',
	SYSTEM.'vendor/illuminate/container/Illuminate/Container/Container.php',
	SYSTEM.'vendor/illuminate/database/Illuminate/Database/Eloquent/Model.php',
	SYSTEM.'vendor/illuminate/database/Illuminate/Database/DatabaseManager.php',
	SYSTEM.'vendor/illuminate/database/Illuminate/Database/ConnectionResolverInterface.php',
	SYSTEM.'vendor/illuminate/database/Illuminate/Database/Connectors/ConnectionFactory.php',
	SYSTEM.'reborn/src/Reborn/Cores/Application.php',
	SYSTEM.'reborn/src/Reborn/Cores/Alias.php',
	SYSTEM.'reborn/src/Reborn/Cores/ErrorFacade.php',
	SYSTEM.'reborn/src/Reborn/Cores/ErrorHandler.php',
	SYSTEM.'reborn/src/Reborn/Cores/Facade.php',
	SYSTEM.'reborn/src/Reborn/Cores/Setting.php',
	SYSTEM.'reborn/src/Reborn/Cores/SiteManager.php',
	SYSTEM.'reborn/src/Reborn/Connector/DB/DBManager.php',
	SYSTEM.'reborn/src/Reborn/Connector/DB/Schema.php',
	SYSTEM.'reborn/src/Reborn/Connector/Sentry/Sentry.php',
	SYSTEM.'reborn/src/Reborn/Connector/Sentry/SymfonySession.php',
	SYSTEM.'reborn/src/Reborn/Connector/Log/LogManager.php',
	SYSTEM.'reborn/src/Reborn/Exception/RbException.php',
	SYSTEM.'reborn/src/Reborn/Exception/EventException.php',
	SYSTEM.'reborn/src/Reborn/Exception/FileNotFoundException.php',
	SYSTEM.'reborn/src/Reborn/Exception/HttpNotFoundException.php',
	SYSTEM.'reborn/src/Reborn/Exception/ModuleException.php',
	SYSTEM.'reborn/src/Reborn/Exception/RouteNotFoundException.php',
	SYSTEM.'reborn/src/Reborn/Exception/TokenNotMatchException.php',
	SYSTEM.'reborn/src/Reborn/Event/EventInterface.php',
	SYSTEM.'reborn/src/Reborn/Event/EventManager.php',
	SYSTEM.'reborn/src/Reborn/Event/SimpleEvent.php',
	SYSTEM.'reborn/src/Reborn/Config/Config.php',
	SYSTEM.'reborn/src/Reborn/Filesystem/File.php',
	SYSTEM.'reborn/src/Reborn/Filesystem/Directory.php',
	SYSTEM.'reborn/src/Reborn/Http/Request.php',
	SYSTEM.'reborn/src/Reborn/Http/Response.php',
	SYSTEM.'reborn/src/Reborn/Http/Redirect.php',
	SYSTEM.'reborn/src/Reborn/Http/Uri.php',
	SYSTEM.'reborn/src/Reborn/Http/Input.php',
	SYSTEM.'reborn/src/Reborn/Http/Cookie.php',
	SYSTEM.'reborn/src/Reborn/Module/AbstractBootstrap.php',
	SYSTEM.'reborn/src/Reborn/Module/AbstractInfo.php',
	SYSTEM.'reborn/src/Reborn/Module/AbstractInstaller.php',
	SYSTEM.'reborn/src/Reborn/Module/ModuleManager.php',
	SYSTEM.'reborn/src/Reborn/Module/Builder.php',
	SYSTEM.'reborn/src/Reborn/Module/Handler/BaseHandler.php',
	SYSTEM.'reborn/src/Reborn/Module/Handler/MultisiteHandler.php',
	SYSTEM.'reborn/src/Reborn/Parser/InfoParser.php',
	SYSTEM.'reborn/src/Reborn/Translate/TranslateManager.php',
	SYSTEM.'reborn/src/Reborn/Translate/Loader/LoaderInterface.php',
	SYSTEM.'reborn/src/Reborn/Translate/Loader/PHPFileLoader.php',
	SYSTEM.'reborn/src/Reborn/Routing/Router.php',
	SYSTEM.'reborn/src/Reborn/Routing/Route.php',
	SYSTEM.'reborn/src/Reborn/Routing/RouteCollection.php',
	SYSTEM.'reborn/src/Reborn/Routing/RouteFacade.php',
	SYSTEM.'reborn/src/Reborn/Routing/Middleware.php',
	SYSTEM.'reborn/src/Reborn/Routing/ControllerResolver.php',
	SYSTEM.'reborn/src/Reborn/Table/Builder.php',
	SYSTEM.'reborn/src/Reborn/Table/DataTable.php',
	SYSTEM.'reborn/src/Reborn/Table/DataTable/UI.php',
	SYSTEM.'reborn/src/Reborn/Util/Flash.php',
	SYSTEM.'reborn/src/Reborn/Util/Hash.php',
	SYSTEM.'reborn/src/Reborn/Util/Html.php',
	SYSTEM.'reborn/src/Reborn/Util/Menu.php',
	SYSTEM.'reborn/src/Reborn/Util/Security.php',
	SYSTEM.'reborn/src/Reborn/Util/Str.php',
	SYSTEM.'reborn/src/Reborn/Util/TagCloud.php',
	SYSTEM.'reborn/src/Reborn/Util/Timezone.php',
	SYSTEM.'reborn/src/Reborn/Widget/AbstractWidget.php',
	SYSTEM.'reborn/src/Reborn/Widget/Widget.php',
	SYSTEM.'reborn/src/Reborn/Presenter/Presentation.php',
	SYSTEM.'reborn/src/Reborn/Presenter/Collection.php',
	SYSTEM.'reborn/src/Reborn/Form/Form.php',
	SYSTEM.'reborn/src/Reborn/Form/UIForm.php',
	SYSTEM.'reborn/src/Reborn/Form/AbstractFormBuilder.php',
	SYSTEM.'reborn/src/Reborn/Form/Blueprint.php',
	SYSTEM.'reborn/src/Reborn/Form/Validation.php',
	SYSTEM.'reborn/src/Reborn/Form/ValidationError.php',
	SYSTEM.'reborn/src/Reborn/Cache/CacheManager.php',
	SYSTEM.'reborn/src/Reborn/Cache/CacheNamespaceStoreInterface.php',
	SYSTEM.'reborn/src/Reborn/Cache/CacheFolderStoreInterface.php',
	SYSTEM.'reborn/src/Reborn/Cache/CacheDriverInterface.php',
	SYSTEM.'reborn/src/Reborn/Cache/Driver/File.php',
	SYSTEM.'reborn/src/Reborn/MVC/Controller/Controller.php',
	SYSTEM.'reborn/src/Reborn/MVC/Controller/AdminController.php',
	SYSTEM.'reborn/src/Reborn/MVC/Controller/PublicController.php',
	SYSTEM.'reborn/src/Reborn/MVC/Controller/PrivateController.php',
	SYSTEM.'reborn/src/Reborn/MVC/Controller/Exception/NotAdminAccessException.php',
	SYSTEM.'reborn/src/Reborn/MVC/Controller/Exception/NotAuthException.php',
	SYSTEM.'reborn/src/Reborn/MVC/Model/Model.php',
	SYSTEM.'reborn/src/Reborn/MVC/Model/Search.php',
	SYSTEM.'reborn/src/Reborn/MVC/View/ViewManager.php',
	SYSTEM.'reborn/src/Reborn/MVC/View/View.php',
	SYSTEM.'reborn/src/Reborn/MVC/View/Theme.php',
	SYSTEM.'reborn/src/Reborn/MVC/View/Template.php',
	SYSTEM.'reborn/src/Reborn/MVC/View/Parser.php',
	SYSTEM.'reborn/src/Reborn/MVC/View/AbstractHandler.php',
	SYSTEM.'reborn/src/Reborn/Asset/Asset.php',
	SYSTEM.'reborn/src/Reborn/Asset/LessResolver.php',
	SYSTEM.'reborn/src/Reborn/Asset/MiniCompressor.php',
	SYSTEM.'reborn/src/Reborn/Pagination/BuilderInterface.php',
	SYSTEM.'reborn/src/Reborn/Pagination/Builder.php',
	SYSTEM.'reborn/src/Reborn/Pagination/PaginationFacade.php',
	)
);
