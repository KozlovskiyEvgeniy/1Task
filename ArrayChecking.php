<?php
require_once('DependencyExclusion.php');
class ArrayChecking
{
    /**
     * @param array $packages
     * @param string $packageName
     *
     * @throws CycleDependencyException
     * @throws ItselfDependency
     * @throws NotHaveDependencyInName
     * @throws NotJointDependencyInKey
     * @throws NotSimilarNameException
     *
     * @return array
     */
    public function getAllPackageDependencies(array $packages, string $packageName): array
    {
        $this->validatePackageDefinitions($packages);
        $ArrayPackages = $this->getAllArrayOfDependencies($packages, $packageName);
        $ArrayPackages = $this->convertMassArrayForOne($ArrayPackages);
        return $ArrayPackages;
    }
    /**
     * @param array $packages
     *
     * @throws CycleDependencyException
     * @throws ItselfDependency
     * @throws NotHaveDependencyInName
     * @throws NotJointDependencyInKey
     * @throws NotSimilarNameException
     *
     * @return void
     */
    public function validatePackageDefinitions(array $packages): void
    {
        $this->checkSimilarityName($packages);
        $this->checkJoinDependencies($packages);
        $this->checkJoinDependenciesKeyInArray($packages);
        $this->checkCycleDependencies($packages, []);
    }
    /**
     * @param array $packages
     *
     * @throws NotSimilarNameException
     *
     * @return void
     */
    private function checkSimilarityName(array $packages): void
    {
        foreach ($packages as $key => $value) {
            if ($key !== $value['name']) {
                throw new NotSimilarNameException("$value[name] is not identically $key");
            }
        }
    }
    /**
     * @param array $packages
     *
     * @throws NotHaveDependencyInName
     *
     * @return void
     */
    private function checkJoinDependencies(array $packages): void
    {
        foreach ($packages as $key => $value) {
            if (array_key_exists("dependencies", $value) === false) {
                throw new NotHaveDependencyInName("$key not have dependencies");
            }
        }
    }
    /**
     * @param array $packages
     *
     * @throws NotJointDependencyInKey
     */
    private function checkJoinDependenciesKeyInArray(array $packages): void
    {
        foreach ($packages as $key => $value) {
            foreach ($value['dependencies'] as $keyDependencies => $valueDependencies) {
                if (!array_key_exists($valueDependencies, $packages)) {
                    throw new NotJointDependencyInKey("$valueDependencies is not member array package $key");
                }
            }
        }
    }
    /**
     * @param array $packages
     * @param array $usedDependencies
     *
     * @throws ItselfDependency
     * @throws CycleDependencyException
     *
     * @return void
     */
    private function checkCycleDependencies(array $packages, array $usedDependencies): void
    {
        foreach ($packages as $key => $package) {
            $dependencies = $package['dependencies'];
            if (!empty($dependencies)) {
                if (in_array($package['name'], $dependencies)) {
                    throw new ItselfDependency('One of packages has dependency with itself');
                }
                $usedDependencies[] = $package['name'];
                foreach ($dependencies as $dependency) {
                    if (in_array($dependency, $usedDependencies)) {
                        throw new CycleDependencyException('Cycle dependency');
                    }
                    $this->checkCycleDependencies($packages[$dependency], $usedDependencies);
                }
            }
        }
    }
    /**
     * @param array $packages
     * @param string $packageName
     *
     * @return array
     */
    private function getAllArrayOfDependencies(array $packages, string $packageName): array
    {
        $fullArrayOfAllNeededPackages = [$packageName];
        foreach ($packages[$packageName]['dependencies'] as $key => $dependencie) {
            if (count($dependencie) !== 0) {
                $fullArrayOfAllNeededPackages[] = $this->getAllArrayOfDependencies($packages, $dependencie);
            }
        }
        return $fullArrayOfAllNeededPackages;
    }
    /**
     * @param array $multipleArrayForFormat
     *
     * @return array
     */
    private function convertMassArrayForOne(array $multipleArrayForFormat): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($multipleArrayForFormat));
        $simpleArray = iterator_to_array($iterator, false);
        $reverseArray = array_reverse($simpleArray);
        $finalDependencies = array_unique($reverseArray);
        return $finalDependencies;
    }
} 
