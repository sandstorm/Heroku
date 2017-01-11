<?php
namespace Sandstorm\Heroku\Command;

/*                                                                        *
 * This script belongs to the Neos Flow package "Sandstorm.Heroku".      *
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class HerokuCommandController extends \Neos\Flow\Cli\CommandController {

	const PROCFILE_CONTENTS = 'web: Packages/Application/Sandstorm.Heroku/Resources/Private/heroku-start.sh';

	/**
	 * Add Heroku Support
	 */
	public function addToProjectCommand() {
		$procfile = FLOW_PATH_ROOT . 'Procfile';
		if (!file_exists($procfile)) {
			$this->outputLine('<comment>[ OK ]</comment> Procfile does not exist. Generating...');
			file_put_contents($procfile, self::PROCFILE_CONTENTS);
		} elseif (file_get_contents($procfile) !== self::PROCFILE_CONTENTS) {
			$this->outputLine('<error>[WARN]</error> Procfile does not contain the expected content. Please remove it to regenerate it.');
		} else {
			$this->outputLine('<info>[ OK ]</info> Procfile exists and contains the expected content.');
		}

		$composerJson = $this->loadComposerJson();

		$this->addExtension('ext-mbstring', $composerJson, '*');
		$this->addExtension('ext-gd', $composerJson, '*');
		$this->addExtension('php', $composerJson, '^5.6.0');

		$expectedPostInstallCmds = array();

		$expectedPostInstallCmds[] = 'Neos\\Flow\\Composer\\InstallerScripts::postUpdateAndInstall';

		if (file_exists(FLOW_PATH_ROOT . 'gerrit.json') && file_exists(FLOW_PATH_ROOT . 'gerrit_update.php')) {
			$expectedPostInstallCmds[] = "if [ -d '.heroku' ] ; then git config --global user.email 'PatchBot@foo.com'; git config --global user.name 'Patch Bot'; fi; php gerrit_update.php";
		}

		$expectedPostInstallCmds[] = "if [ -d '.heroku' ] ; then FLOW_CONTEXT=Production/Heroku ./flow cache:warmup; fi";

		if (isset($composerJson['scripts']['post-install-cmd']) && $composerJson['scripts']['post-install-cmd'] === $expectedPostInstallCmds) {
			$this->outputLine('<info>[ OK ]</info> post-install-cmd is as expected');
		} else {
			$composerJson['scripts']['post-install-cmd'] = $expectedPostInstallCmds;
			$this->saveComposerJson($composerJson);
			$this->outputLine('<comment>[ OK ]</comment> Updated post-install-cmd.');
		}
	}

	protected function addExtension($extensionIdentifier, &$composerJson, $version) {
		if (!isset($composerJson['require'][$extensionIdentifier])) {
			$composerJson['require'][$extensionIdentifier] = $version;
			$this->saveComposerJson($composerJson);
			$this->outputLine('<comment>[ OK ]</comment> Added ' . $extensionIdentifier . ' to composer.json.');
		} elseif ($composerJson['require'][$extensionIdentifier] !== $version) {
			$this->outputLine('<error>[WARN]</error> composer.json requires ' . $extensionIdentifier . ' in a different version instead of "' . $version . '".');
		} else {
			$this->outputLine('<info>[ OK ]</info> composer.json requires ' . $extensionIdentifier);
		}
}

	public function saveComposerJson($composerJson) {
		$composerFile = FLOW_PATH_ROOT . 'composer.json';

		file_put_contents($composerFile, json_encode($composerJson, JSON_PRETTY_PRINT));
	}

	protected function loadComposerJson() {
		$composerFile = FLOW_PATH_ROOT . 'composer.json';
		$composerFileContents = file_get_contents($composerFile);
		return json_decode($composerFileContents, TRUE);
	}
}